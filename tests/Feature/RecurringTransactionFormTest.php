<?php

namespace Tests\Feature;

use App\Filament\Resources\RecurringTransactions\Pages\CreateRecurringTransaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecurringTransactionFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_amount_is_parsed_and_stored_correctly()
    {
        $user = User::factory()->create();

        $account = Account::create(["user_id" => $user->id, "name" => "A", "type" => "Cash", "initial_balance" => 0, "current_balance" => 0]);
        $category = Category::create(["user_id" => $user->id, "name" => "Gaji", "type" => "income", "color" => "#000"]);

        $start = Carbon::now()->startOfDay();
        $next = $start->copy()->addWeeks(2);

        Livewire::actingAs($user)
            ->test(CreateRecurringTransaction::class)
            ->set('data.description', 'Pay Test')
            ->set('data.amount', '1.900.000')
            ->set('data.account_id', $account->id)
            ->set('data.category_id', $category->id)
            ->set('data.frequency', 'monthly')
            ->set('data.start_date', $start->format('Y-m-d'))
            ->set('data.next_run_date', $next->format('Y-m-d'))
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('recurring_transactions', [
            'description' => 'Pay Test',
            'amount' => '1900000.00',
            'account_id' => $account->id,
            'category_id' => $category->id,
        ]);
    }
}
