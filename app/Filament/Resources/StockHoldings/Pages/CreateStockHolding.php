<?php

namespace App\Filament\Resources\StockHoldings\Pages;

use App\Filament\Resources\StockHoldings\StockHoldingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStockHolding extends CreateRecord
{
    protected static string $resource = StockHoldingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
