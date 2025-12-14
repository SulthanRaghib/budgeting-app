<?php

namespace App\Filament\Resources\Accounts;

use App\Filament\Resources\Accounts\Pages;
use App\Models\Account;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'name';

    protected static UnitEnum|string|null $navigationGroup = 'Tabungan';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')->required()->maxLength(255),
            Select::make('type')->options([
                'Cash' => 'Cash',
                'Bank' => 'Bank',
                'E-Wallet' => 'E-Wallet',
            ])->required(),
            ColorPicker::make('color'),
            TextInput::make('initial_balance')
                ->label('Initial Balance')
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
            TextInput::make('current_balance')
                ->label('Current Balance')
                ->numeric()
                ->disabled()
                ->dehydrated(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('type')->sortable(),
            TextColumn::make('initial_balance')
                ->label('Initial')
                ->numeric(0, ',', '.')
                ->prefix('Rp ')
                ->alignEnd(),
            TextColumn::make('current_balance')
                ->label('Balance')
                ->numeric(0, ',', '.')
                ->prefix('Rp ')
                ->alignEnd()
                ->weight('bold'),
        ])->headerActions([
            CreateAction::make(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure the account is associated with the current user on creation
        $data['user_id'] = $data['user_id'] ?? \Illuminate\Support\Facades\Auth::id();

        // Ensure current_balance is set to initial_balance on creation
        if (! isset($data['current_balance']) || $data['current_balance'] === null) {
            $data['current_balance'] = $data['initial_balance'] ?? 0;
        }

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data, $record): array
    {
        // If the user changed the initial balance in the edit form, recompute
        // the account's current_balance as: new_initial + transactions_net
        if (array_key_exists('initial_balance', $data)) {
            $newInitial = (float) ($data['initial_balance'] ?? 0);

            $income = \App\Models\Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
                ->where('transactions.account_id', $record->id)
                ->where('categories.type', 'income')
                ->sum('transactions.amount');

            $expense = \App\Models\Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
                ->where('transactions.account_id', $record->id)
                ->where('categories.type', 'expense')
                ->sum('transactions.amount');

            $net = (float) $income - (float) $expense;

            $data['current_balance'] = $newInitial + $net;
        }

        return $data;
    }
}
