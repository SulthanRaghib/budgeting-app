<?php

namespace App\Filament\Resources\RecurringTransactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RecurringTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('description')
                    ->default(null),
                Select::make('frequency')
                    ->options(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'])
                    ->required(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('next_run_date')
                    ->required(),
                DatePicker::make('end_date'),
                DateTimePicker::make('last_run_at'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
