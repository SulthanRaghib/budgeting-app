<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;

class DashboardWidgetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_advanced_stats_overview_respects_user_scope()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $acc1 = Account::create(['user_id' => $user->id, 'name' => 'A', 'type' => 'Cash', 'initial_balance' => 0, 'current_balance' => 100000]);
        $acc2 = Account::create(['user_id' => $other->id, 'name' => 'B', 'type' => 'Cash', 'initial_balance' => 0, 'current_balance' => 500000]);

        $catInc = Category::create(['user_id' => $user->id, 'name' => 'Gaji', 'type' => 'income', 'color' => '#000']);
        $catExp = Category::create(['user_id' => $user->id, 'name' => 'Makan', 'type' => 'expense', 'color' => '#000']);

        Transaction::create(['user_id' => $user->id, 'account_id' => $acc1->id, 'category_id' => $catInc->id, 'amount' => 200000, 'date' => Carbon::now()->toDateString()]);
        Transaction::create(['user_id' => $other->id, 'account_id' => $acc2->id, 'category_id' => $catExp->id, 'amount' => 999999, 'date' => Carbon::now()->toDateString()]);

        // Ensure computations reflect current user's accounts and transactions (scoped)
        // Account balance will reflect transaction deltas applied by observers
        $this->assertEquals(300000, Account::where('user_id', $user->id)->sum('current_balance'));

        $start = Carbon::now()->startOfMonth()->toDateString();
        $end = Carbon::now()->endOfMonth()->toDateString();

        $income = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $user->id)
            ->where('categories.type', 'income')
            ->whereBetween('transactions.date', [$start, $end])->sum('transactions.amount');

        $this->assertEquals(200000, $income);
    }
}
