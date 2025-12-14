<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountsRecalculate extends Command
{
    protected $signature = 'accounts:recalculate {--user=* : Recalculate only for specific user ids}';

    protected $description = 'Recalculate account current_balance based on transactions and initial_balance';

    public function handle(): int
    {
        $userIds = $this->option('user');

        $query = Account::query();
        if ($userIds) {
            $query->whereIn('user_id', $userIds);
        }

        $bar = $this->output->createProgressBar($query->count());
        $bar->start();

        foreach ($query->cursor() as $account) {
            $balance = (float) $account->initial_balance;

            $txs = DB::table('transactions')
                ->where('account_id', $account->id)
                ->join('categories', 'transactions.category_id', '=', 'categories.id')
                ->select('transactions.amount', 'categories.type')
                ->get();

            foreach ($txs as $tx) {
                if ($tx->type === 'income') {
                    $balance += (float) $tx->amount;
                } else {
                    $balance -= (float) $tx->amount;
                }
            }

            $account->update(['current_balance' => $balance]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Recalculation complete.');

        return 0;
    }
}
