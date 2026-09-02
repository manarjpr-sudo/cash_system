<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider; // <-- هذا السطر الجديد (use)

return [
    AppServiceProvider::class,
    AuthServiceProvider::class, // <-- وهذا السطر الجديد في المصفوفة
];