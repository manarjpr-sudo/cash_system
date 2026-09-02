<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Operation;
use App\Observers\OperationObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Operation::observe(OperationObserver::class);
    }
}