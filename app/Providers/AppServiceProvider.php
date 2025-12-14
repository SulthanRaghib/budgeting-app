<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SavingTransaction;
use App\Observers\SavingTransactionObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        SavingTransaction::observe(SavingTransactionObserver::class);
    }
}
