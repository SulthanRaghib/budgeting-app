<?php

namespace App\Filament\Resources\StockHoldings;

use App\Filament\Resources\StockHoldings\Pages\CreateStockHolding;
use App\Filament\Resources\StockHoldings\Pages\EditStockHolding;
use App\Filament\Resources\StockHoldings\Pages\ListStockHoldings;
use App\Models\StockHolding;
use App\Services\YahooFinanceService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class StockHoldingResource extends Resource
{
    protected static ?string $model = StockHolding::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $recordTitleAttribute = 'ticker';

    protected static UnitEnum|string|null $navigationGroup = 'Investasi';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Portofolio Saham';

    protected static ?string $pluralModelLabel = 'Portofolio Saham';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Detail Portofolio')
                    ->schema([
                        Hidden::make('user_id')
                            ->default(Auth::id()),

                        TextInput::make('ticker')
                            ->label('Kode Saham')
                            ->placeholder('Contoh: BBCA, TLKM, BMRI')
                            ->required()
                            ->maxLength(10)
                            ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                            ->dehydrateStateUsing(fn($state) => strtoupper($state)),

                        TextInput::make('total_shares')
                            ->label('Total Lot')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Diperbarui otomatis dari transaksi'),

                        TextInput::make('average_price')
                            ->label('Harga Rata-rata')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Dihitung otomatis'),

                        TextInput::make('current_price')
                            ->label('Harga Saat Ini')
                            ->prefix('Rp')
                            ->numeric()
                            ->nullable()
                            ->helperText('Perbarui manual atau dari data pasar'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticker')
                    ->label('Kode Saham')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->size('lg'),

                TextColumn::make('total_shares')
                    ->label('Jumlah Lot')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('average_price')
                    ->label('Harga Rata-rata')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('current_price')
                    ->label('Harga Sekarang')
                    ->money('IDR')
                    ->placeholder('Belum diatur')
                    ->sortable(),

                TextColumn::make('total_cost')
                    ->label('Total Modal')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('market_value')
                    ->label('Nilai Pasar')
                    ->money('IDR')
                    ->placeholder('-'),

                TextColumn::make('unrealized_gain_loss')
                    ->label('Keuntungan/Rugi')
                    ->money('IDR')
                    ->color(fn($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray'))
                    ->icon(fn($state) => $state > 0 ? 'heroicon-o-arrow-trending-up' : ($state < 0 ? 'heroicon-o-arrow-trending-down' : null))
                    ->placeholder('-'),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('ticker')
            ->filters([
                //
            ])
            ->actions([
                // Fetch price from Yahoo Finance API
                Action::make('fetchPrice')
                    ->label('Ambil Harga')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Ambil Harga dari Yahoo Finance')
                    ->modalDescription(fn(StockHolding $record) => "Apakah Anda ingin mengambil harga terbaru untuk {$record->ticker} dari Yahoo Finance?")
                    ->modalSubmitActionLabel('Ya, Ambil Harga')
                    ->action(function (StockHolding $record): void {
                        $service = new YahooFinanceService();
                        $data = $service->fetchStockData($record->ticker);

                        if ($data && isset($data['current_price'])) {
                            $oldPrice = $record->current_price;
                            $record->update(['current_price' => $data['current_price']]);

                            $priceFormatted = 'Rp ' . number_format($data['current_price'], 0, ',', '.');
                            $change = '';

                            if ($oldPrice) {
                                $diff = $data['current_price'] - $oldPrice;
                                $diffFormatted = 'Rp ' . number_format(abs($diff), 0, ',', '.');
                                $change = $diff >= 0 ? " (+{$diffFormatted})" : " (-{$diffFormatted})";
                            }

                            Notification::make()
                                ->success()
                                ->title('Harga Berhasil Diperbarui')
                                ->body("{$record->ticker}: {$priceFormatted}{$change}")
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Mengambil Harga')
                                ->body("Tidak dapat menemukan data untuk {$record->ticker}. Pastikan kode saham valid.")
                                ->send();
                        }
                    }),

                // Manual update price
                Action::make('updatePrice')
                    ->label('Input Manual')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->form([
                        TextInput::make('current_price')
                            ->label('Harga Saat Ini')
                            ->prefix('Rp')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function (StockHolding $record, array $data): void {
                        $record->update([
                            'current_price' => $data['current_price'],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Harga Diperbarui')
                            ->body("{$record->ticker}: Rp " . number_format($data['current_price'], 0, ',', '.'))
                            ->send();
                    }),

                ActionGroup::make([
                    EditAction::make()->label('Edit'),
                    DeleteAction::make()->label('Hapus'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Bulk update prices from Yahoo Finance
                    BulkAction::make('bulkFetchPrices')
                        ->label('Ambil Harga Terpilih')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Ambil Harga dari Yahoo Finance')
                        ->modalDescription('Apakah Anda ingin mengambil harga terbaru untuk semua saham yang dipilih?')
                        ->modalSubmitActionLabel('Ya, Ambil Semua')
                        ->action(function (Collection $records): void {
                            $service = new YahooFinanceService();
                            $success = 0;
                            $failed = 0;

                            foreach ($records as $record) {
                                if ($record->total_shares <= 0) {
                                    continue;
                                }

                                $service->clearCache($record->ticker);
                                $data = $service->fetchStockData($record->ticker);

                                if ($data && isset($data['current_price'])) {
                                    $record->update(['current_price' => $data['current_price']]);
                                    $success++;
                                } else {
                                    $failed++;
                                }

                                // Small delay to avoid rate limiting
                                usleep(300000); // 300ms
                            }

                            if ($success > 0) {
                                Notification::make()
                                    ->success()
                                    ->title('Harga Berhasil Diperbarui')
                                    ->body("{$success} saham berhasil diperbarui" . ($failed > 0 ? ", {$failed} gagal" : ""))
                                    ->send();
                            } else {
                                Notification::make()
                                    ->danger()
                                    ->title('Gagal Memperbarui Harga')
                                    ->body('Tidak ada saham yang berhasil diperbarui')
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()->label('Hapus Terpilih'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockHoldings::route('/'),
            'create' => CreateStockHolding::route('/create'),
            'edit' => EditStockHolding::route('/{record}/edit'),
        ];
    }
}
