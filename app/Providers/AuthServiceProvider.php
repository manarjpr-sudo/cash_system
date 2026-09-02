<?php

namespace App\Providers;

use App\Models\Operation;
use App\Policies\OperationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * ربط النماذج بسياساتها (Policies)
     */
    protected $policies = [
        Operation::class => OperationPolicy::class,
    ];

    /**
     * تسجيل أي خدمات مصادقة / تفويض.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}