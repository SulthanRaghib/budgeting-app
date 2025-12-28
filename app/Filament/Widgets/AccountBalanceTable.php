<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;

class AccountBalanceTable extends Widget
{
    protected ?string $heading = 'Account Balances';

    protected array|string|int $columnSpan = 1;

    // Render eagerly; balances are critical information
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.account-balance-table';

    public function getAccounts()
    {
        $userId = Auth::id();

        return Account::where('user_id', $userId)
            ->orderByDesc('current_balance')
            ->get(['id', 'name', 'type', 'current_balance']);
    }
}
