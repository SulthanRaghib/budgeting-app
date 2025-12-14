<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;

class InspectLatestTransaction extends Command
{
    protected $signature = 'inspect:latest-transaction';

    protected $description = 'Print details about the latest transaction and related account/category';

    public function handle(): int
    {
        $t = Transaction::latest()->first();
        if (! $t) {
            $this->info('No transactions found.');
            return 0;
        }

        $this->line('Transaction: ' . $t->id);
        $this->line('  amount: ' . $t->amount);
        $this->line('  account_id: ' . $t->account_id);
        $this->line('  category_type: ' . ($t->category ? $t->category->type : 'no category'));
        $this->line('Account current_balance: ' . ($t->account ? $t->account->current_balance : 'no account'));

        $this->line('\nAll accounts:');
        foreach (\App\Models\Account::all() as $a) {
            $this->line(sprintf("  [%d] %s — initial: %s, current: %s", $a->id, $a->name, $a->initial_balance, $a->current_balance));
        }

        return 0;
    }
}
