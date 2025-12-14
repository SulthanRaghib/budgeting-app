<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('type')
                    ->default(null),
                TextInput::make('color')
                    ->default(null),
                TextInput::make('initial_balance')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('current_balance')
                    ->required()
                    ->numeric()
                    ->default(0.0),
            ]);
    }
}
