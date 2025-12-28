<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringTransaction;
use Livewire\Livewire;
use Carbon\Carbon;
use App\Filament\Resources\RecurringTransactions\Pages\EditRecurringTransaction;

class RecurringTransactionEditFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_amount_displays_with_thousands_separator_on_edit()
    {
        $user = User::factory()->create();
        $account = Account::create(["user_id" => $user->id, "name" => "A", "type" => "Cash", "initial_balance" => 0, "current_balance" => 0]);
        $category = Category::create(["user_id" => $user->id, "name" => "Gaji", "type" => "income", "color" => "#000"]);

        $rt = RecurringTransaction::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => 198000,
            'description' => 'Test Edit',
            'frequency' => 'monthly',
            'start_date' => Carbon::today(),
            'next_run_date' => Carbon::today()->addWeek(),
            'is_active' => true,
        ]);

        Livewire::actingAs($user)
            ->test(EditRecurringTransaction::class, ['record' => $rt->getKey()])
            ->assertSet('data.amount', '198.000');
    }
}
