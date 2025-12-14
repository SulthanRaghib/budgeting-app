<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function afterCreate(): void
    {
        $account = $this->record->account;
        if ($account) {
            Notification::make()
                ->success()
                ->title('Transaction created')
                ->body(sprintf('Account %s balance: Rp %s', $account->name, number_format($account->current_balance, 0, ',', '.')))
                ->send();

            $this->dispatch('accountsUpdated');
        }
    }
}
