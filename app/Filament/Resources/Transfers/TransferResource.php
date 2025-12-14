<?php

namespace App\Filament\Resources\Transfers;

use App\Filament\Resources\Transfers\Pages;
use App\Models\Transfer;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn as Col;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class TransferResource extends Resource
{
    protected static ?string $model = Transfer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowRight;

    protected static ?string $recordTitleAttribute = 'id';
    protected static UnitEnum|string|null $navigationGroup = 'Anggaran & Transaksi';

    public static function form(Schema $schema): Schema
    {
        $userId = Auth::id();

        return $schema->schema([
            Select::make('from_account_id')
                ->label('From Account')
                ->relationship('fromAccount', 'name', fn($query) => $userId ? $query->where('user_id', $userId) : $query)
                ->preload()
                ->searchable()
                ->required()
                ->reactive(),

            Select::make('to_account_id')
                ->label('To Account')
                ->options(fn($get) => \App\Models\Account::where('user_id', $userId)
                    ->when($get('from_account_id'), fn($q, $from) => $q->where('id', '!=', $from))
                    ->pluck('name', 'id'))
                ->preload()
                ->searchable()
                ->required()
                ->reactive()
                ->helperText(fn($get) => ($to = \App\Models\Account::find($get('to_account_id'))) ? sprintf('Balance: Rp %s', number_format((float)$to->current_balance, 0, ',', '.')) : 'Select a destination account to see balance')
                ->rule('different:from_account_id'),

            TextInput::make('amount')
                ->label('Amount')
                ->required()
                ->prefix('Rp ')
                ->minValue(0)
                ->reactive()
                ->helperText(fn($get) => ($from = \App\Models\Account::find($get('from_account_id'))) ? sprintf('Available: Rp %s', number_format((float)$from->current_balance, 0, ',', '.')) : 'Select a source account to see available balance')
                ->extraInputAttributes([
                    'inputmode' => 'numeric',
                    'onfocus' => "(function(){this.value = (this.value || '').toString().replace(/[^0-9]/g, '');}).call(this)",
                    'oninput' => "(function(){let v = this.value.replace(/[^0-9]/g,''); this.value = v ? (Number(v).toLocaleString('id-ID')) : '';}).call(this)",
                    'onblur' => "(function(){let v = this.value.replace(/[^0-9]/g,''); this.value = v ? (Number(v).toLocaleString('id-ID')) : '';}).call(this)",
                ])
                ->formatStateUsing(fn($state) => $state !== null && $state !== '' ? number_format((float) $state, 0, ',', '.') : null)
                ->dehydrateStateUsing(fn($state) => $state !== null && $state !== '' ? (float) preg_replace('/[^0-9]/', '', (string) $state) : null),

            DatePicker::make('date')->required()->default(now()),

            Textarea::make('notes')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            BadgeColumn::make('fromAccount.name')->label('From')->colors([
                'danger' => fn($state) => true,
            ]),

            BadgeColumn::make('toAccount.name')->label('To')->colors([
                'success' => fn($state) => true,
            ]),

            TextColumn::make('amount')
                ->label('Amount')
                ->numeric(0, ',', '.')
                ->prefix('Rp ')
                ->alignEnd()
                ->weight('bold')
                ->sortable(),

            TextColumn::make('date')->label('Date')->date('d M Y'),
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

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        // Attach user id and validate sufficient balance
        $data['user_id'] = Auth::id();

        if (isset($data['from_account_id'], $data['amount'])) {
            $from = \App\Models\Account::find($data['from_account_id']);
            $amount = (float) $data['amount'];
            if ($from && $amount > (float) $from->current_balance) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => ['Insufficient balance in source account.'],
                ]);
            }
        }

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data, $record): array
    {
        // same validation for edit
        if (isset($data['from_account_id'], $data['amount'])) {
            $from = \App\Models\Account::find($data['from_account_id']);
            $amount = (float) $data['amount'];
            if ($from && $amount > (float) $from->current_balance && ($record === null || $record->from_account_id !== $data['from_account_id'] || (float)$record->amount !== $amount)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => ['Insufficient balance in source account.'],
                ]);
            }
        }

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransfers::route('/'),
            'create' => Pages\CreateTransfer::route('/create'),
            'edit' => Pages\EditTransfer::route('/{record}/edit'),
        ];
    }
}
