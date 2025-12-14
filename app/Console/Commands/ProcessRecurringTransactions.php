<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcessRecurringTransactions extends Command
{
    protected $signature = 'app:process-recurring';

    protected $description = 'Process due recurring transactions and create real transactions.';

    public function handle()
    {
        $today = Carbon::today();

        $due = RecurringTransaction::where('is_active', true)
            ->whereDate('next_run_date', '<=', $today)
            ->get();

        $count = 0;

        foreach ($due as $recurrence) {
            DB::transaction(function () use ($recurrence, &$count, $today) {
                // process multiple missed runs until next_run_date > today
                while ($recurrence->is_active && $recurrence->next_run_date && $recurrence->next_run_date->lte($today)) {
                    $runDate = $recurrence->next_run_date->copy();

                    // create transaction
                    Transaction::create([
                        'user_id' => $recurrence->user_id,
                        'category_id' => $recurrence->category_id,
                        'account_id' => $recurrence->account_id,
                        'date' => $runDate->toDateString(),
                        'amount' => $recurrence->amount,
                        'description' => $recurrence->description,
                    ]);

                    $recurrence->last_run_at = now();

                    // advance next_run_date
                    $next = $this->calculateNextDate($recurrence->next_run_date, $recurrence->frequency);
                    $recurrence->next_run_date = $next;

                    // if end_date set and next exceeds end_date, deactivate
                    if ($recurrence->end_date && $recurrence->next_run_date->gt($recurrence->end_date)) {
                        $recurrence->is_active = false;
                    }

                    $recurrence->save();

                    $count++;

                    // safety: prevent infinite loops
                    if ($count > 10000) break;
                }
            });
        }

        $this->info("Processed {$count} transaction(s).");

        return 0;
    }

    protected function calculateNextDate($date, $frequency)
    {
        $d = Carbon::parse($date);

        return match ($frequency) {
            'daily' => $d->addDay(),
            'weekly' => $d->addWeek(),
            'monthly' => $d->addMonth(),
            'yearly' => $d->addYear(),
            default => $d->addMonth(),
        };
    }
}
