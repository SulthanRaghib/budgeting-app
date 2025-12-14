<?php

namespace App\Filament\Resources\Transactions;

use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Models\Transaction;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'description';
    protected static UnitEnum|string|null $navigationGroup = 'Anggaran & Transaksi';

    public static function form(Schema $schema): Schema
    {
        $userId = Auth::id();

        return $schema
            ->schema([
                Section::make('Transaction')
                    ->schema([
                        Hidden::make('user_id')->default($userId),

                        TextInput::make('user_name')
                            ->label('User')
                            ->default(Auth::user()?->name)
                            ->disabled()
                            ->dehydrated(false),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name', fn($query) => $userId ? $query->where('user_id', $userId) : $query)
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('account_id')
                            ->label('Account')
                            ->relationship('account', 'name', fn($query) => $userId ? $query->where('user_id', $userId) : $query)
                            ->searchable()
                            ->preload()
                            ->required(),

                        DatePicker::make('date')
                            ->default(now())
                            ->required(),

                        TextInput::make('amount')
                            ->label('Amount')
                            ->required()
                            ->prefix('Rp ')
                            ->extraInputAttributes([
                                'inputmode' => 'numeric',
                                'onfocus' => "(function(){this.value = (this.value || '').toString().replace(/[^0-9]/g, '');}).call(this)",
                                'oninput' => "(function(){let v = this.value.replace(/[^0-9]/g,''); this.value = v ? (Number(v).toLocaleString('id-ID')) : '';}).call(this)",
                                'onblur' => "(function(){let v = this.value.replace(/[^0-9]/g,''); this.value = v ? (Number(v).toLocaleString('id-ID')) : '';}).call(this)",
                            ])
                            ->formatStateUsing(fn($state) => $state !== null && $state !== '' ? number_format((float) $state, 0, ',', '.') : null)
                            ->dehydrateStateUsing(fn($state) => $state !== null && $state !== '' ? (float) preg_replace('/[^0-9]/', '', (string) $state) : null),

                        TextInput::make('description')
                            ->maxLength(255),

                        FileUpload::make('image')
                            ->directory('transactions')
                            ->image()
                            ->imageEditor(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label('Category')
                    ->view('filament.components.category-badge')
                    ->searchable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('account.name')
                    ->label('Account')
                    ->toggleable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->sortable()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->weight('bold'),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(30)
                    ->searchable()
                    ->wrap(),

                ImageColumn::make('image')
                    ->label('Image')
                    ->circular(),
            ])
            ->filters([
                SelectFilter::make('category')->label('Category')->relationship('category', 'name', fn($query) => (Auth::id() ? $query->where('user_id', Auth::id()) : $query)),
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('date_from')->label('From'),
                        DatePicker::make('date_until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['date_from'] ?? null) {
                            $query->whereDate('date', '>=', $data['date_from']);
                        }
                        if ($data['date_until'] ?? null) {
                            $query->whereDate('date', '<=', $data['date_until']);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data, $record): array
    {
        $data['user_id'] = $record->user_id ?? Auth::id();

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }
}
