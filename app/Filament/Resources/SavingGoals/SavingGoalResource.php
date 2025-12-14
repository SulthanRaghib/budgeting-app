<?php

namespace App\Filament\Resources\SavingGoals;

use App\Filament\Resources\SavingGoals\Pages\CreateSavingGoal;
use App\Filament\Resources\SavingGoals\Pages\EditSavingGoal;
use App\Filament\Resources\SavingGoals\Pages\ListSavingGoals;
use App\Filament\Resources\SavingGoals\Schemas\SavingGoalForm;
use App\Filament\Resources\SavingGoals\Tables\SavingGoalsTable;
use App\Models\SavingGoal;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class SavingGoalResource extends Resource
{
    protected static ?string $model = SavingGoal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $recordTitleAttribute = 'SavingGoal';

    public static function form(Schema $schema): Schema
    {
        $userId = Auth::id();

        return $schema
            ->schema([
                Hidden::make('user_id')->default($userId),

                Section::make('Goal Details')
                    ->schema([
                        TextInput::make('user_name')
                            ->label('User')
                            ->default(Auth::user()?->name)
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('name')
                            ->label('Name')
                            ->required(),

                        TextInput::make('target_amount')
                            ->label('Target Amount')
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

                        TextInput::make('current_amount')
                            ->label('Current Amount')
                            ->default(0)
                            ->prefix('Rp ')
                            ->extraInputAttributes([
                                'inputmode' => 'numeric',
                                'onfocus' => "(function(){this.value = (this.value || '').toString().replace(/[^0-9]/g, '');}).call(this)",
                                'oninput' => "(function(){let v = this.value.replace(/[^0-9]/g,''); this.value = v ? (Number(v).toLocaleString('id-ID')) : '';}).call(this)",
                                'onblur' => "(function(){let v = this.value.replace(/[^0-9]/g,''); this.value = v ? (Number(v).toLocaleString('id-ID')) : '';}).call(this)",
                            ])
                            ->formatStateUsing(fn($state) => $state !== null && $state !== '' ? number_format((float) $state, 0, ',', '.') : null)
                            ->dehydrateStateUsing(fn($state) => $state !== null && $state !== '' ? (float) preg_replace('/[^0-9]/', '', (string) $state) : 0),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->default(now())
                                    ->required(),

                                DatePicker::make('target_date')
                                    ->label('Target Date')
                                    ->nullable()
                                    ->rules(['nullable', 'after:start_date']),
                            ]),

                        ToggleButtons::make('status')
                            ->label('Status')
                            ->options([
                                'ongoing' => 'Ongoing',
                                'completed' => 'Completed',
                            ])
                            ->default('ongoing')
                            ->inline()
                            ->colors([
                                'ongoing' => 'primary',
                                'completed' => 'success',
                            ])
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'primary' => 'ongoing',
                        'success' => 'completed',
                    ]),

                TextColumn::make('target_amount')
                    ->label('Target')
                    ->formatStateUsing(fn($state) => $state !== null && $state !== '' ? 'Rp ' . number_format((float) $state, 0, ',', '.') : null)
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('current_amount')
                    ->label('Saved')
                    ->formatStateUsing(fn($state) => $state !== null && $state !== '' ? 'Rp ' . number_format((float) $state, 0, ',', '.') : 'Rp 0')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('progress')
                    ->label('Progress')
                    ->getStateUsing(
                        fn($record) => ($record->target_amount > 0)
                            ? round(($record->current_amount / $record->target_amount) * 100) . '%'
                            : '0%'
                    )
                    ->color(fn($state) => (int) rtrim($state, '%') < 50 ? 'danger' : ((int) rtrim($state, '%') < 80 ? 'warning' : 'success')),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSavingGoals::route('/'),
            'create' => CreateSavingGoal::route('/create'),
            'edit' => EditSavingGoal::route('/{record}/edit'),
        ];
    }
}
