<?php

namespace App\Filament\Resources\RecurringTransactions;

use App\Filament\Resources\RecurringTransactions\Pages;
use App\Models\RecurringTransaction;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class RecurringTransactionResource extends Resource
{
    protected static ?string $model = RecurringTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $recordTitleAttribute = 'description';
    protected static UnitEnum|string|null $navigationGroup = 'Anggaran & Transaksi';

    public static function form(Schema $schema): Schema
    {
        $userId = Auth::id();

        return $schema->schema([
            TextInput::make('description')->maxLength(255),
            TextInput::make('amount')
                ->required()
                ->prefix('Rp ')
                ->minValue(0)
                ->extraInputAttributes([
                    'inputmode' => 'numeric',
                    'onfocus' => "(function(){this.value = (this.value || '').toString().replace(/[^0-9]/g, '');}).call(this)",
                    'oninput' => "(function(){let v = this.value.replace(/[^0-9]/g,''); this.value = v ? (Number(v).toLocaleString('id-ID')) : '';}).call(this)",
                    'onblur' => "(function(){let v = this.value.replace(/[^0-9]/g,''); this.value = v ? (Number(v).toLocaleString('id-ID')) : '';}).call(this)",
                ])
                ->dehydrateStateUsing(fn($state) => $state !== null ? (float) (fn($s) => str_replace(',', '.', str_replace('.', '', $s)))(preg_replace('/[^0-9.,]/', '', (string) $state)) : null)
                ->formatStateUsing(fn($state) => $state !== null && $state !== '' ? number_format((float) $state, 0, ',', '.') : null),
            Select::make('account_id')->relationship('account', 'name', fn($q) => $q?->where('user_id', $userId))->searchable()->required()->preload(),
            Select::make('category_id')->relationship('category', 'name', fn($q) => $q?->where('user_id', $userId))->searchable()->required()->preload(),

            Hidden::make('user_id')->default(fn() => Auth::id()),

            Select::make('frequency')->options([
                'daily' => 'Daily',
                'weekly' => 'Weekly',
                'monthly' => 'Monthly',
                'yearly' => 'Yearly',
            ])->required(),

            DatePicker::make('start_date')->required(),
            DatePicker::make('next_run_date')->required(),
            DatePicker::make('end_date'),
            Toggle::make('is_active')->default(true),
            Textarea::make('notes')->rows(2)->placeholder('Optional notes for this recurring transaction'),
        ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('description')->label('Description')->limit(30),
            TextColumn::make('amount')->numeric(0, ',', '.')->prefix('Rp ')->alignEnd()->weight('bold'),
            BadgeColumn::make('frequency')->colors(['primary' => fn($s) => true]),
            TextColumn::make('next_run_date')->label('Next Run')->date('d M Y'),
            BooleanColumn::make('is_active')->label('Active')->sortable(),
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
            'index' => Pages\ListRecurringTransactions::route('/'),
            'create' => Pages\CreateRecurringTransaction::route('/create'),
            'edit' => Pages\EditRecurringTransaction::route('/{record}/edit'),
        ];
    }
}
