<?php

namespace App\Filament\Resources\Accounts\Pages;

use App\Filament\Resources\Accounts\AccountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function afterCreate(): void
    {
        // Notify other components (widgets/tables) to refresh
        if (method_exists($this, 'dispatch')) {
            $this->dispatch('accountsUpdated');
        }

        \Filament\Notifications\Notification::make()
            ->success()
            ->title('Account created')
            ->body('New account created and balances updated')
            ->send();
    }
}
