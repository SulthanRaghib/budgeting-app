<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login;

class CustomLogin extends Login
{
    protected string $view = 'filament.pages.auth.custom-login';

    public function getLayout(): string
    {
        return 'filament-panels::components.layout.base';
    }
}
