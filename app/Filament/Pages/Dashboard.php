<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-s-home';

    public function getWidgets(): array
    {
        $widgets = [
            // Top: full width
            \App\Filament\Widgets\AdvancedStatsOverview::class,
            // Small stats row (legacy)
            \App\Filament\Widgets\StatsOverview::class,

            // Middle row (2 columns)
            \App\Filament\Widgets\CashFlowChart::class,
            \App\Filament\Widgets\ExpenseCategoryChart::class,

            // Bottom row
            \App\Filament\Widgets\AccountBalanceTable::class,
        ];

        // Append BudgetProgressWidget only if the class exists (some installs may not have it)
        if (class_exists(\App\Filament\Widgets\BudgetProgressWidget::class)) {
            $widgets[] = \App\Filament\Widgets\BudgetProgressWidget::class;
        }

        return $widgets;
    }

    public function getColumns(): int | array
    {
        // Use a 2 column grid: widgets can declare their own span
        return 2;
    }
}
