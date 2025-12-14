<?php

namespace App\Filament\Resources\Accounts\Pages;

use App\Filament\Resources\Accounts\AccountResource;
use Filament\Resources\Pages\EditRecord;

class EditAccount extends EditRecord
{
    protected static string $resource = AccountResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function afterSave(): void
    {
        if (method_exists($this, 'dispatch')) {
            $this->dispatch('accountsUpdated');
        }

        \Filament\Notifications\Notification::make()
            ->success()
            ->title('Account updated')
            ->body('Account changes saved and balances refreshed')
            ->send();
    }
}
