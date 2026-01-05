<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CashFlowChart extends ChartWidget
{
    protected ?string $heading = 'Arus Kas Bulanan';

    protected ?string $description = 'Pemasukan vs Pengeluaran harian';

    // Span half the dashboard width
    protected int|string|array $columnSpan = 1;

    // Max height for the chart
    protected ?string $maxHeight = '280px';

    // Render eagerly so dashboard shows charts without a large placeholder gap
    protected static bool $isLazy = false;

    // Filter options
    public ?string $filter = 'this_month';

    protected function getFilters(): ?array
    {
        return [
            'this_month' => 'Bulan Ini',
            'last_month' => 'Bulan Lalu',
            'last_3_months' => '3 Bulan Terakhir',
        ];
    }

    protected function getData(): array
    {
        $userId = Auth::id();

        // Determine date range based on filter
        [$start, $end] = match ($this->filter) {
            'last_month' => [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ],
            'last_3_months' => [
                Carbon::now()->subMonths(2)->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
            default => [ // this_month
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
        };

        $days = [];
        $labels = [];
        $period = $start->copy();

        while ($period->lte($end)) {
            $labels[] = $period->format('d M');
            $days[] = $period->toDateString();
            $period->addDay();
        }

        // Fetch income data
        $income = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->where('categories.type', 'income')
            ->whereBetween('transactions.date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('date(transactions.date) as day, sum(transactions.amount) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        // Fetch expense data
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
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => $incomeData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $expenseData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.12)',
                    'fill' => true,
                    'tension' => 0.3,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 15,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
                                }
                                return 'Rp ' + value;
                            },
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(128, 128, 128, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 13,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        footerFont: {
                            size: 12,
                            weight: 'bold'
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const value = context.raw || 0;
                                return label + new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                }).format(value);
                            },
                            footer: function(tooltipItems) {
                                let income = 0;
                                let expense = 0;

                                tooltipItems.forEach(function(item) {
                                    if (item.dataset.label === 'Pemasukan') {
                                        income = item.raw || 0;
                                    } else if (item.dataset.label === 'Pengeluaran') {
                                        expense = item.raw || 0;
                                    }
                                });

                                const netFlow = income - expense;
                                const formatted = new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                }).format(Math.abs(netFlow));

                                const sign = netFlow >= 0 ? '+' : '-';
                                const status = netFlow >= 0 ? '📈 Surplus' : '📉 Defisit';

                                return status + ': ' + sign + ' ' + formatted;
                            }
                        }
                    }
                }
            }
        JS);
    }
}
