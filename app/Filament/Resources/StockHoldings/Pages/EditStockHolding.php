<?php

namespace App\Filament\Resources\StockHoldings\Pages;

use App\Filament\Resources\StockHoldings\StockHoldingResource;
use Filament\Resources\Pages\EditRecord;

class EditStockHolding extends EditRecord
{
    protected static string $resource = StockHoldingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
