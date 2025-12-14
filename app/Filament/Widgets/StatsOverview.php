<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected ?string $heading = 'Stats Overview';

    // Listen for account changes and refresh this widget when they occur
    protected $listeners = ['accountsUpdated' => '$refresh'];

    protected function getStats(): array
    {
        $userId = Auth::id();

        $totalIncome = \App\Models\Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->where('categories.type', 'income')
            ->sum('transactions.amount');

        $totalExpense = \App\Models\Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->where('categories.type', 'expense')
            ->sum('transactions.amount');

        $totalSaved = \App\Models\SavingTransaction::where('user_id', $userId)->sum('amount');

        // Available balance should reflect actual account balances (current balances)
        $totalAccountBalance = \App\Models\Account::where('user_id', $userId)->sum('current_balance');

        $freeCash = (float) $totalAccountBalance;
        $format = fn($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');

        return [
            Stat::make('Available Balance', $format($freeCash))
                ->description('Real money ready to spend')
                ->icon('heroicon-m-wallet')
                ->color($freeCash > 0 ? 'success' : 'danger'),

            Stat::make('Total Expenses', $format($totalExpense))
                ->description('Money gone (Transactions)')
                ->icon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Total Savings', $format($totalSaved))
                ->description('Money in Goals/Piggy bank')
                ->icon('heroicon-m-banknotes')
                ->color('info'),
        ];
    }
}
