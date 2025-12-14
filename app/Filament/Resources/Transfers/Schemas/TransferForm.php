<?php

namespace App\Filament\Resources\Transfers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('from_account_id')
                    ->required()
                    ->numeric(),
                TextInput::make('to_account_id')
                    ->required()
                    ->numeric(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('date')
                    ->required(),
                TextInput::make('notes')
                    ->default(null),
            ]);
    }
}
