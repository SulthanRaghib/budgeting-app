<?php

namespace App\Filament\Resources\Transfers\Pages;

use App\Filament\Resources\Transfers\TransferResource;
use Filament\Resources\Pages\EditRecord;

class EditTransfer extends EditRecord
{
    protected static string $resource = TransferResource::class;

    public function afterSave(): void
    {
        if (method_exists($this, 'dispatch')) {
            $this->dispatch('accountsUpdated');
        }

        \Filament\Notifications\Notification::make()
            ->success()
            ->title('Transfer updated')
            ->body('Balances updated')
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
