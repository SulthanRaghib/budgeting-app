<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Carbon;

class ExpenseCategoryChart extends Widget
{
    protected ?string $heading = 'Expenses by Category (This Month)';

    protected array|string|int $columnSpan = 1;

    // Render eagerly so dashboard shows charts without a large placeholder gap
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.expense-category-chart';

    public function getData(): array
    {
        $userId = Auth::id();

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $results = Transaction::where('transactions.user_id', $userId)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('categories.type', 'expense')
            ->whereBetween('transactions.date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('categories.id as category_id, categories.name as category_name, categories.color as color, sum(transactions.amount) as total')
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($results as $row) {
            $labels[] = $row->category_name;
            $data[] = (float) $row->total;
            $colors[] = $row->color ?: sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }
}
