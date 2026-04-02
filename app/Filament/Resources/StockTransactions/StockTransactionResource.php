<?php

namespace App\Filament\Resources\StockTransactions;

use App\Filament\Resources\StockTransactions\Pages\CreateStockTransaction;
use App\Filament\Resources\StockTransactions\Pages\EditStockTransaction;
use App\Filament\Resources\StockTransactions\Pages\ListStockTransactions;
use App\Models\StockHolding;
use App\Models\StockTransaction;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class StockTransactionResource extends Resource
{
    protected static ?string $model = StockTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $recordTitleAttribute = 'id';

    protected static UnitEnum|string|null $navigationGroup = 'Investasi';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Transaksi Saham';

    protected static ?string $pluralModelLabel = 'Transaksi Saham';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Detail Transaksi')
                    ->schema([
                        Hidden::make('user_id')
                            ->default(Auth::id()),

                        ToggleButtons::make('type')
                            ->label('Jenis Transaksi')
                            ->options([
                                'buy' => 'Beli',
                                'sell' => 'Jual',
                                'dividend' => 'Dividen',
                            ])
                            ->colors([
                                'buy' => 'success',
                                'sell' => 'danger',
                                'dividend' => 'info',
                            ])
                            ->icons([
                                'buy' => 'heroicon-o-arrow-down-circle',
                                'sell' => 'heroicon-o-arrow-up-circle',
                                'dividend' => 'heroicon-o-banknotes',
                            ])
                            ->inline()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('stock_holding_id', null)),

                        Select::make('stock_holding_id')
                            ->label('Pilih Saham')
                            ->relationship('stockHolding', 'ticker', fn(Builder $query) => $query->where('user_id', Auth::id()))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->createOptionForm([
                                TextInput::make('ticker')
                                    ->label('Kode Saham')
                                    ->required()
                                    ->maxLength(10)
                                    ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                                    ->dehydrateStateUsing(fn($state) => strtoupper($state)),
                                Hidden::make('user_id')
                                    ->default(Auth::id()),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $holding = StockHolding::firstOrCreate(
                                    ['user_id' => Auth::id(), 'ticker' => strtoupper($data['ticker'])],
                                    ['total_shares' => 0, 'average_price' => 0]
                                );
                                return $holding->id;
                            })
                            ->helperText(fn(Get $get) => $get('type') === 'sell'
                                ? 'Pilih saham yang akan dijual'
                                : 'Pilih saham atau buat baru'),

                        Select::make('account_id')
                            ->label('Rekening Dana (RDN)')
                            ->relationship('account', 'name', fn(Builder $query) => $query->where('user_id', Auth::id()))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Rekening untuk arus kas (debit saat beli, kredit saat jual/dividen)'),

                        DatePicker::make('date')
                            ->label('Tanggal Transaksi')
                            ->default(now())
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Detail Jumlah')
                    ->schema([
                        TextInput::make('shares')
                            ->label('Jumlah Lot')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(1)
                            ->visible(fn(Get $get) => in_array($get('type'), ['buy', 'sell']))
                            ->helperText(function (Get $get) {
                                if ($get('type') === 'sell' && $get('stock_holding_id')) {
                                    $holding = StockHolding::find($get('stock_holding_id'));
                                    return $holding ? "Tersedia: {$holding->total_shares} lot" : null;
                                }
                                return null;
                            }),

                        TextInput::make('price')
                            ->label(fn(Get $get) => $get('type') === 'dividend' ? 'Jumlah Dividen' : 'Harga per Lembar')
                            ->prefix('Rp')
                            ->numeric()
                            ->required()
                            ->minValue(0),

                        TextInput::make('fee')
                            ->label('Biaya Transaksi')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->visible(fn(Get $get) => in_array($get('type'), ['buy', 'sell'])),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('stockHolding.ticker')
                    ->label('Kode Saham')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'buy' => 'success',
                        'sell' => 'danger',
                        'dividend' => 'info',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'buy' => 'Beli',
                        'sell' => 'Jual',
                        'dividend' => 'Dividen',
                    }),

                TextColumn::make('shares')
                    ->label('Jumlah Lot')
                    ->numeric()
                    ->placeholder('-'),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR'),

                TextColumn::make('fee')
                    ->label('Biaya')
                    ->money('IDR')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->color(fn($record) => match ($record->type) {
                        'buy' => 'danger',
                        'sell', 'dividend' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('account.name')
                    ->label('Rekening')
                    ->toggleable(),

                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        'buy' => 'Beli',
                        'sell' => 'Jual',
                        'dividend' => 'Dividen',
                    ]),
                SelectFilter::make('stock_holding_id')
                    ->label('Kode Saham')
                    ->relationship('stockHolding', 'ticker', fn(Builder $query) => $query->where('user_id', Auth::id())),
            ])
            ->actions([
                EditAction::make()->label('Edit'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
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
            'index' => ListStockTransactions::route('/'),
            'create' => CreateStockTransaction::route('/create'),
            'edit' => EditStockTransaction::route('/{record}/edit'),
        ];
    }
}
