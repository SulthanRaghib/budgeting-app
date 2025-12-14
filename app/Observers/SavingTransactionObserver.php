<?php

namespace App\Observers;

use App\Models\SavingTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SavingTransactionObserver
{
    public function creating(SavingTransaction $transaction): void
    {
        // Ensure the transaction belongs to the authenticated user
        if (! $transaction->user_id) {
            $transaction->user_id = Auth::id();
        }
    }

    public function created(SavingTransaction $transaction): void
    {
        // Update related saving goal atomically
        DB::transaction(function () use ($transaction) {
            $goal = $transaction->savingGoal()->lockForUpdate()->first();

            if (! $goal) {
                return;
            }

            // Increment current_amount
            $goal->increment('current_amount', $transaction->amount);

            // Refresh to get latest value
            $goal->refresh();

            // If goal reached or exceeded, mark as completed
            if ($goal->current_amount >= $goal->target_amount && $goal->status !== 'completed') {
                $goal->status = 'completed';
                $goal->save();
            }
        });
    }

    public function updated(SavingTransaction $transaction): void
    {
        // Adjust goal(s) when a transaction is changed
        DB::transaction(function () use ($transaction) {
            $original = $transaction->getOriginal();
            $oldGoalId = $original['saving_goal_id'] ?? null;
            $oldAmount = isset($original['amount']) ? (float) $original['amount'] : 0.0;

            $newGoalId = $transaction->saving_goal_id;
            $newAmount = (float) $transaction->amount;

            if ($oldGoalId && ($oldGoalId != $newGoalId)) {
                // Remove from old goal
                $oldGoal = \App\Models\SavingGoal::lockForUpdate()->find($oldGoalId);
                if ($oldGoal) {
                    $oldGoal->decrement('current_amount', $oldAmount);
                    $oldGoal->refresh();
                    if ($oldGoal->current_amount < 0) {
                        $oldGoal->current_amount = 0;
                        $oldGoal->save();
                    }
                    if ($oldGoal->status === 'completed' && $oldGoal->current_amount < $oldGoal->target_amount) {
                        $oldGoal->status = 'ongoing';
                        $oldGoal->save();
                    }
                }

                // Add to new goal
                $newGoal = \App\Models\SavingGoal::lockForUpdate()->find($newGoalId);
                if ($newGoal) {
                    $newGoal->increment('current_amount', $newAmount);
                    $newGoal->refresh();
                    if ($newGoal->current_amount >= $newGoal->target_amount && $newGoal->status !== 'completed') {
                        $newGoal->status = 'completed';
                        $newGoal->save();
                    }
                }
            } else {
                // Same goal, adjust by delta
                $delta = $newAmount - $oldAmount;
                if ($delta !== 0 && $newGoalId) {
                    $goal = \App\Models\SavingGoal::lockForUpdate()->find($newGoalId);
                    if ($goal) {
                        if ($delta > 0) {
                            $goal->increment('current_amount', $delta);
                        } else {
                            $goal->decrement('current_amount', abs($delta));
                        }
                        $goal->refresh();
                        if ($goal->current_amount >= $goal->target_amount && $goal->status !== 'completed') {
                            $goal->status = 'completed';
                            $goal->save();
                        } elseif ($goal->status === 'completed' && $goal->current_amount < $goal->target_amount) {
                            $goal->status = 'ongoing';
                            $goal->save();
                        }
                        if ($goal->current_amount < 0) {
                            $goal->current_amount = 0;
                            $goal->save();
                        }
                    }
                }
            }
        });
    }

    public function deleted(SavingTransaction $transaction): void
    {
        // Subtract amount from goal when a transaction is deleted
        DB::transaction(function () use ($transaction) {
            $goal = $transaction->savingGoal()->lockForUpdate()->first();

            if (! $goal) {
                return;
            }

            $goal->decrement('current_amount', $transaction->amount);
            $goal->refresh();
            if ($goal->current_amount < 0) {
                $goal->current_amount = 0;
                $goal->save();
            }

            // If goal fell below target, mark as ongoing
            if ($goal->status === 'completed' && $goal->current_amount < $goal->target_amount) {
                $goal->status = 'ongoing';
                $goal->save();
            }
        });
    }
}
