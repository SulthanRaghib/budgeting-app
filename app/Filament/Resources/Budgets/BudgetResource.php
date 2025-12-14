<?php

namespace App\Filament\Resources\Budgets;

use App\Filament\Resources\Budgets\Pages\CreateBudget;
use App\Filament\Resources\Budgets\Pages\EditBudget;
use App\Filament\Resources\Budgets\Pages\ListBudgets;
use App\Models\Budget;
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
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?string $recordTitleAttribute = 'Budget';

    public static function form(Schema $schema): Schema
    {
        $userId = Auth::id();

        return $schema
            ->schema([
                Hidden::make('user_id')
                    ->default($userId),

                Section::make('Budget')
                    ->schema([
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
                            ->required()
                            ->rules([
                                function ($attribute, $value, $fail) use ($userId) {
                                    $recordId = request()->route('record');
                                    $query = Budget::where('user_id', $userId)->where('category_id', $value);
                                    if ($recordId) {
                                        $query->where('id', '<>', $recordId);
                                    }
                                    if ($query->exists()) {
                                        $fail('The category has already been taken.');
                                    }
                                },
                            ]),

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
                            ->dehydrateStateUsing(fn($state) => $state !== null && $state !== '' ? (float) preg_replace('/[^0-9]/', '', (string) $state) : null)
                            ->helperText('Monthly limit for this category'),

                        Select::make('period')
                            ->label('Period')
                            ->options([
                                'monthly' => 'Monthly',
                                'yearly' => 'Yearly',
                            ])
                            ->default('monthly')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state !== null && $state !== '' ? 'Rp ' . number_format((float) $state, 0, ',', '.') : null)
                    ->alignEnd(),

                TextColumn::make('period')
                    ->label('Period')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->toggleable(),
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

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data, $record): array
    {
        // Ensure user_id is kept on update
        $data['user_id'] = $record->user_id ?? Auth::id();

        return $data;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBudgets::route('/'),
            'create' => CreateBudget::route('/create'),
            'edit' => EditBudget::route('/{record}/edit'),
        ];
    }
}
