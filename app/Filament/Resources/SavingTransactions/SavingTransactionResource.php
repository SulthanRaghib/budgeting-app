<?php

namespace App\Filament\Resources\SavingTransactions;

use App\Filament\Resources\SavingTransactions\Pages\CreateSavingTransaction;
use App\Filament\Resources\SavingTransactions\Pages\EditSavingTransaction;
use App\Filament\Resources\SavingTransactions\Pages\ListSavingTransactions;
use App\Filament\Resources\SavingTransactions\Schemas\SavingTransactionForm;
use App\Filament\Resources\SavingTransactions\Tables\SavingTransactionsTable;
use App\Models\SavingTransaction;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SavingTransactionResource extends Resource
{
    protected static ?string $model = SavingTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static UnitEnum|string|null $navigationGroup = 'Tabungan';

    protected static ?string $recordTitleAttribute = 'SavingTransaction';

    public static function form(Schema $schema): Schema
    {
        $userId = Auth::id();

        return $schema
            ->schema([
                Hidden::make('user_id')->default($userId),

                // show user name as disabled input
                TextInput::make('user_name')
                    ->label('User')
                    ->default(Auth::user()?->name)
                    ->disabled()
                    ->dehydrated(false),

                Select::make('saving_goal_id')
                    ->label('Goal / Impian')
                    ->relationship('savingGoal', 'name', fn($query) => $userId ? $query->where('user_id', $userId) : $query)
                    ->searchable()
                    ->preload()
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
                    ->rules([
                        function () use ($userId) {
                            $recordId = request()->route('record');

                            $totalIncome = \App\Models\Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
                                ->where('transactions.user_id', $userId)
                                ->where('categories.type', 'income')
                                ->sum('transactions.amount');

                            $totalExpense = \App\Models\Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
                                ->where('transactions.user_id', $userId)
                                ->where('categories.type', 'expense')
                                ->sum('transactions.amount');

                            $savingsQuery = \App\Models\SavingTransaction::where('user_id', $userId);
                            if ($recordId) {
                                $savingsQuery->where('id', '<>', $recordId);
                            }
                            $totalSavings = $savingsQuery->sum('amount');

                            $available = (float) $totalIncome - ((float) $totalExpense + (float) $totalSavings);

                            return new class($available) implements \Illuminate\Contracts\Validation\Rule {
                                protected $available;

                                public function __construct($available)
                                {
                                    $this->available = $available;
                                }

                                public function passes($attribute, $value)
                                {
                                    $amount = is_numeric($value) ? (float) $value : (float) preg_replace('/[^0-9]/', '', (string) $value);

                                    return $amount <= (float) $this->available;
                                }

                                public function message()
                                {
                                    return 'Insufficient balance. You only have IDR ' . number_format($this->available, 0, ',', '.') . ' available.';
                                }
                            };
                        },
                    ])
                    ->formatStateUsing(fn($state) => $state !== null && $state !== '' ? number_format((float) $state, 0, ',', '.') : null)
                    ->dehydrateStateUsing(fn($state) => $state !== null && $state !== '' ? (float) preg_replace('/[^0-9]/', '', (string) $state) : null),

                DatePicker::make('date')
                    ->label('Date')
                    ->default(now())
                    ->required(),

                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('savingGoal.name')
                    ->label('Goal')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn($state) => $state !== null && $state !== '' ? 'Rp ' . number_format((float) $state, 0, ',', '.') : null)
                    ->alignEnd(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(30)
                    ->toggleable(),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // user_id is assigned automatically in the SavingTransactionObserver

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSavingTransactions::route('/'),
            'create' => CreateSavingTransaction::route('/create'),
            'edit' => EditSavingTransaction::route('/{record}/edit'),
        ];
    }
}
