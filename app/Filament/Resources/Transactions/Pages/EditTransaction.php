<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function afterSave(): void
    {
        $account = $this->record->account;
        if ($account) {
            Notification::make()
                ->success()
                ->title('Transaction updated')
                ->body(sprintf('Account %s balance: Rp %s', $account->name, number_format($account->current_balance, 0, ',', '.')))
                ->send();

            $this->dispatch('accountsUpdated');
        }
    }
}
