<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Illuminate\Support\Facades\Auth;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Category';

    public static function form(Schema $schema): Schema
    {
        $userId = Auth::id();
        return $schema
            ->schema([
                Section::make('Category')
                    ->schema([
                        Hidden::make('user_id')->default($userId),

                        TextInput::make('user_name')
                            ->label('User')
                            ->default(Auth::user()?->name)
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('name')
                            ->required(),

                        ToggleButtons::make('type')
                            ->options([
                                'income' => 'Income',
                                'expense' => 'Expense',
                            ])
                            ->inline()
                            ->default('expense')
                            ->colors([
                                'income' => 'success',
                                'expense' => 'danger',
                            ]),

                        ColorPicker::make('color'),

                        Select::make('icon')
                            ->label('Icon')
                            ->options(fn() => config('icons.available', []))
                            ->placeholder('Select an icon')
                            ->helperText('Choose a Heroicon (value stored like: heroicon-o-home)')
                            ->extraAttributes(['id' => 'icon-select']),

                        SchemaView::make('filament.components.icon-grid'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->toggleable()
                    ->badge()
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                    ]),

                ColorColumn::make('color'),

                TextColumn::make('icon')
                    ->label('Icon')
                    ->view('filament.components.icon-column'),
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

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
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
}
