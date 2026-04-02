<?php

namespace App\Filament\Resources\StockHoldings\Pages;

use App\Filament\Resources\StockHoldings\StockHoldingResource;
use App\Models\StockHolding;
use App\Services\YahooFinanceService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListStockHoldings extends ListRecords
{
    protected static string $resource = StockHoldingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Update All Prices Action
            Action::make('updateAllPrices')
                ->label('Update Semua Harga')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Update Semua Harga Saham')
                ->modalDescription('Apakah Anda ingin mengambil harga terbaru untuk SEMUA saham dari Yahoo Finance? Proses ini mungkin membutuhkan beberapa waktu.')
                ->modalSubmitActionLabel('Ya, Update Semua')
                ->action(function (): void {
                    // Get ALL stocks for this user (regardless of shares count)
                    $holdings = StockHolding::where('user_id', Auth::id())->get();

                    if ($holdings->isEmpty()) {
                        Notification::make()
                            ->warning()
                            ->title('Tidak Ada Data')
                            ->body('Tidak ada saham dalam portofolio Anda.')
                            ->send();
                        return;
                    }

                    $service = new YahooFinanceService();
                    $success = 0;
                    $failed = 0;
                    $results = [];

                    foreach ($holdings as $holding) {
                        $service->clearCache($holding->ticker);
                        $data = $service->fetchStockData($holding->ticker);

                        if ($data && isset($data['current_price'])) {
                            $oldPrice = $holding->current_price;
                            $holding->update(['current_price' => $data['current_price']]);

                            $change = '';
                            if ($oldPrice) {
                                $diff = $data['current_price'] - $oldPrice;
                                $change = $diff >= 0 ? '+' : '';
                                $change .= number_format($diff, 0, ',', '.');
                            }

                            $results[] = "{$holding->ticker}: Rp " . number_format($data['current_price'], 0, ',', '.') . ($change ? " ({$change})" : '');
                            $success++;
                        } else {
                            $results[] = "{$holding->ticker}: ❌ Gagal";
                            $failed++;
                        }

                        // Delay to avoid rate limiting
                        usleep(300000);
                    }

                    if ($success > 0) {
                        Notification::make()
                            ->success()
                            ->title("✅ {$success} Saham Diperbarui")
                            ->body(implode("\n", array_slice($results, 0, 5)) . (count($results) > 5 ? "\n... dan " . (count($results) - 5) . " lainnya" : ''))
                            ->duration(10000)
                            ->send();
                    }

                    if ($failed > 0 && $success === 0) {
                        Notification::make()
                            ->danger()
                            ->title('Gagal Memperbarui')
                            ->body("Semua {$failed} saham gagal diperbarui. Periksa koneksi internet atau kode saham.")
                            ->send();
                    }
                }),

            CreateAction::make()
                ->label('Tambah Saham Baru'),
        ];
    }
}
