<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_account_sets_user_and_current_balance()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $account = Account::create([
            'user_id' => null,
            'name' => 'Wallet',
            'type' => 'E-Wallet',
            'initial_balance' => 100000,
        ]);

        $this->assertEquals($user->id, $account->user_id);
        $this->assertEquals(100000, (float) $account->current_balance);
    }

    public function test_updating_initial_balance_recomputes_current_from_transactions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'Bank',
            'type' => 'Bank',
            'initial_balance' => 50000,
            'current_balance' => 50000,
        ]);

        // Create categories and transactions for this account
        $incomeCategory = \App\Models\Category::create([
            'user_id' => $user->id,
            'name' => 'Gaji',
            'type' => 'income',
            'color' => '#34D399',
            'icon' => 'heroicon-o-cash',
        ]);

        $expenseCategory = \App\Models\Category::create([
            'user_id' => $user->id,
            'name' => 'Makan',
            'type' => 'expense',
            'color' => '#FBBF24',
            'icon' => 'heroicon-o-utensils',
        ]);

        // Add transactions: +50_000 income, -20_000 expense => net +30_000
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'category_id' => $incomeCategory->id,
            'account_id' => $account->id,
            'date' => now(),
            'amount' => 50000,
        ]);

        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'category_id' => $expenseCategory->id,
            'account_id' => $account->id,
            'date' => now(),
            'amount' => 20000,
        ]);

        // Now change initial to 80_000. Expected current = 80_000 + (50_000 - 20_000) = 110_000
        $account->initial_balance = 80000;
        $account->save();

        $this->assertEquals(80000, (float) $account->fresh()->initial_balance);
        $this->assertEquals(110000, (float) $account->fresh()->current_balance);
    }
}
