<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Livewire\Livewire;
use Carbon\Carbon;

class DashboardRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_our_widgets_and_charts()
    {
        $user = User::factory()->create();
        $acc = Account::create(['user_id' => $user->id, 'name' => 'BCA', 'type' => 'Bank', 'initial_balance' => 0, 'current_balance' => 100000]);
        $catInc = Category::create(['user_id' => $user->id, 'name' => 'Gaji', 'type' => 'income', 'color' => '#000']);

        Transaction::create(['user_id' => $user->id, 'account_id' => $acc->id, 'category_id' => $catInc->id, 'amount' => 50000, 'date' => Carbon::now()->toDateString()]);

        // Widgets are lazy-loaded on the dashboard page; test widgets directly instead.
        Livewire::actingAs($user)
            ->test(\App\Filament\Widgets\AdvancedStatsOverview::class)
            ->assertSee('Total Net Worth')
            ->assertSee('Income This Month');

        // Small stats block should still be present
        Livewire::actingAs($user)
            ->test(\App\Filament\Widgets\StatsOverview::class)
            ->assertSee('Available Balance')
            ->assertSee('Total Expenses');

        Livewire::actingAs($user)
            ->test(\App\Filament\Widgets\CashFlowChart::class)
            ->assertSee('<canvas', false);

        Livewire::actingAs($user)
            ->test(\App\Filament\Widgets\ExpenseCategoryChart::class)
            ->assertSee('<canvas', false)
            ->assertSee('No expense data for this month');
    }
}
