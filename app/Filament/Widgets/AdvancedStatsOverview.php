<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

class AdvancedStatsOverview extends BaseWidget
{
    protected ?string $heading = 'Financial Overview';

    // Make this widget full width in a 2-column layout
    protected array|string|int $columnSpan = 2;

    // Render eagerly so the overview is visible immediately
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $userId = Auth::id();

        $totalNetWorth = Account::where('user_id', $userId)->sum('current_balance');

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $incomeThisMonth = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->where('categories.type', 'income')
            ->whereBetween('transactions.date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->sum('transactions.amount');

        $expenseThisMonth = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->where('categories.type', 'expense')
            ->whereBetween('transactions.date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->sum('transactions.amount');

        $netCashFlow = $incomeThisMonth - $expenseThisMonth;

        $fmt = fn($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');

        return [
            Stat::make('Total Net Worth', $fmt($totalNetWorth))
                ->description('All accounts combined')
                ->icon('heroicon-m-building-library')
                ->color('primary'),

            Stat::make('Income This Month', $fmt($incomeThisMonth))
                ->icon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Expense This Month', $fmt($expenseThisMonth))
                ->icon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Net Cash Flow', $fmt($netCashFlow))
                ->description('Savings potential this month')
                ->icon('heroicon-m-presentation-chart-line')
                ->color($netCashFlow >= 0 ? 'success' : 'danger'),
        ];
    }
}
