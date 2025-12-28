<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use Carbon\Carbon;

class ProcessRecurringTransactionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_creates_transactions_and_updates_next_run()
    {
        $user = User::factory()->create();
        $account = Account::create(['user_id' => $user->id, 'name' => 'A', 'type' => 'Cash', 'initial_balance' => 0, 'current_balance' => 0]);
        $category = Category::create(['user_id' => $user->id, 'name' => 'Gaji', 'type' => 'income', 'color' => '#000']);

        $start = Carbon::today()->subDay();
        $next = Carbon::today();

        RecurringTransaction::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => 100000,
            'description' => 'Monthly Pay',
            'frequency' => 'monthly',
            'start_date' => $start->toDateString(),
            'next_run_date' => $next->toDateString(),
            'end_date' => null,
            'is_active' => true,
        ]);

        $this->artisan('app:process-recurring')->assertExitCode(0);

        $this->assertDatabaseHas('transactions', ['amount' => 100000, 'user_id' => $user->id]);
        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseCount('recurring_transactions', 1);
        $rt = RecurringTransaction::first();
        $this->assertTrue($rt->next_run_date->gt($next));
    }
}
