<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

class CashFlowChart extends Widget
{
    protected ?string $heading = 'Cash Flow Trend (Daily)';

    // span half the dashboard width
    protected array|string|int $columnSpan = 1;

    // Render eagerly so dashboard shows charts without a large placeholder gap
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.cash-flow-chart';

    public function getData(): array
    {
        $userId = Auth::id();

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $days = [];
        $labels = [];
        $period = $start->copy();
        while ($period->lte($end)) {
            $labels[] = $period->format('d M');
            $days[] = $period->toDateString();
            $period->addDay();
        }

        $income = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->where('categories.type', 'income')
            ->whereBetween('transactions.date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('date(transactions.date) as day, sum(transactions.amount) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $expense = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->where('categories.type', 'expense')
            ->whereBetween('transactions.date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('date(transactions.date) as day, sum(transactions.amount) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $incomeData = [];
        $expenseData = [];

        foreach ($days as $d) {
            $incomeData[] = isset($income[$d]) ? (float) $income[$d] : 0;
            $expenseData[] = isset($expense[$d]) ? (float) $expense[$d] : 0;
        }

        return [
            'labels' => $labels,
            'income' => $incomeData,
            'expense' => $expenseData,
        ];
    }
}
