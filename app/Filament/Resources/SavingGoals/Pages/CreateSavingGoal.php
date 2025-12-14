<?php

namespace App\Filament\Resources\SavingGoals\Pages;

use App\Filament\Resources\SavingGoals\SavingGoalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSavingGoal extends CreateRecord
{
    protected static string $resource = SavingGoalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
