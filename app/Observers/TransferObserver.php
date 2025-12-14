<?php

namespace App\Observers;

use App\Models\Transfer;
use Illuminate\Support\Facades\DB;

class TransferObserver
{
    public function creating(Transfer $transfer): void
    {
        // ensure user_id is set when creating via Filament form
        if (empty($transfer->user_id)) {
            $transfer->user_id = \Illuminate\Support\Facades\Auth::id();
        }

        // Start an explicit DB transaction and acquire locks to prevent races
        DB::beginTransaction();

        try {
            $amount = (float) ($transfer->amount ?? 0);
            $accountIds = array_filter([$transfer->from_account_id, $transfer->to_account_id]);
            $accounts = \App\Models\Account::whereIn('id', $accountIds)->lockForUpdate()->get()->keyBy('id');

            $from = $accounts->get($transfer->from_account_id);

            if ($from && (float) $from->current_balance < $amount) {
                DB::rollBack();

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => ['Insufficient balance in source account.'],
                ]);
            }

            // leave transaction open; it will be committed in created() after applying deltas
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            throw $e;
        }
    }

    public function created(Transfer $transfer): void
    {
        try {
            // Lock involved accounts to avoid race conditions
            $accountIds = array_filter([$transfer->from_account_id, $transfer->to_account_id]);
            $accounts = \App\Models\Account::whereIn('id', $accountIds)->lockForUpdate()->get()->keyBy('id');

            $from = $accounts->get($transfer->from_account_id);
            $to = $accounts->get($transfer->to_account_id);

            $amount = (float) $transfer->amount;

            // Re-check balance atomically (protect against concurrent changes between creating and created)
            if ($from && (float) $from->current_balance < $amount) {
                // Rollback the open transaction to cancel the insert
                DB::rollBack();

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => ['Insufficient balance in source account.'],
                ]);
            }

            if ($from) {
                $from->decrement('current_balance', $amount);
            }

            if ($to) {
                $to->increment('current_balance', $amount);
            }

            DB::commit();
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            throw $e;
        }
    }

    public function updated(Transfer $transfer): void
    {
        // Handle changes to amount or accounts by reverting original then applying new
        $original = $transfer->getOriginal();

        DB::transaction(function () use ($transfer, $original) {
            $accountIds = array_unique(array_filter([
                $original['from_account_id'] ?? null,
                $original['to_account_id'] ?? null,
                $transfer->from_account_id,
                $transfer->to_account_id,
            ]));

            $accounts = \App\Models\Account::whereIn('id', $accountIds)->lockForUpdate()->get()->keyBy('id');

            // Revert old
            $oldFrom = $accounts->get($original['from_account_id']);
            $oldTo = $accounts->get($original['to_account_id']);
            $oldAmount = (float) ($original['amount'] ?? 0);

            if ($oldFrom) {
                $oldFrom->increment('current_balance', $oldAmount);
            }

            if ($oldTo) {
                $oldTo->decrement('current_balance', $oldAmount);
            }

            // Apply new
            $newFrom = $accounts->get($transfer->from_account_id);
            $newTo = $accounts->get($transfer->to_account_id);
            $newAmount = (float) $transfer->amount;

            if ($newFrom && (float) $newFrom->current_balance < $newAmount) {
                // revert the revert to keep original state consistent
                if ($oldFrom) {
                    $oldFrom->decrement('current_balance', $oldAmount);
                }
                if ($oldTo) {
                    $oldTo->increment('current_balance', $oldAmount);
                }

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => ['Insufficient balance in source account for updated transfer.'],
                ]);
            }

            if ($newFrom) {
                $newFrom->decrement('current_balance', $newAmount);
            }

            if ($newTo) {
                $newTo->increment('current_balance', $newAmount);
            }
        });
    }

    public function deleted(Transfer $transfer): void
    {
        DB::transaction(function () use ($transfer) {
            $accountIds = array_filter([$transfer->from_account_id, $transfer->to_account_id]);
            $accounts = \App\Models\Account::whereIn('id', $accountIds)->lockForUpdate()->get()->keyBy('id');

            $from = $accounts->get($transfer->from_account_id);
            $to = $accounts->get($transfer->to_account_id);

            if ($from) {
                $from->increment('current_balance', $transfer->amount);
            }

            if ($to) {
                $to->decrement('current_balance', $transfer->amount);
            }
        });
    }
}
