<?php

namespace App\Filament\Resources\Transfers\Pages;

use App\Filament\Resources\Transfers\TransferResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransfer extends CreateRecord
{
    protected static string $resource = TransferResource::class;

    public function create(bool $another = false): void
    {
        try {
            parent::create($another);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $message = '';
            try {
                $message = (string) implode("\n", $e->validator->errors()->all());
            } catch (\Throwable $ex) {
                $message = $e->getMessage();
            }

            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Transfer failed')
                ->body($message ?: 'Validation failed')
                ->send();

            // Do not rethrow - we handled showing the message
            return;
        }
    }

    public function afterCreate(): void
    {
        if (method_exists($this, 'dispatch')) {
            $this->dispatch('accountsUpdated');
        }

        \Filament\Notifications\Notification::make()
            ->success()
            ->title('Transfer created')
            ->body('Balances updated')
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
