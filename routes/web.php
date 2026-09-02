<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OperationViewController;
use App\Http\Controllers\CustomerViewController;
use App\Http\Controllers\TransactionViewController;
use App\Http\Controllers\UserViewController;
use App\Http\Controllers\RoleViewController;



Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'permission:view_dashboard'])
    ->name('dashboard');


Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | Operations
    |--------------------------------------------------------------------------
    */

    Route::get('/operations', [OperationViewController::class, 'index'])
        ->middleware('permission:view_operations')
        ->name('operations.index');

    Route::get('/operations/create', [OperationViewController::class, 'create'])
        ->middleware('permission:create_operations')
        ->name('operations.create');

    Route::post('/operations', [OperationViewController::class, 'store'])
        ->middleware('permission:create_operations')
        ->name('operations.store');

    Route::get('/operations/{operation}', [OperationViewController::class, 'show'])
        ->middleware('permission:view_operations')
        ->name('operations.show');

    Route::post('/operations/{operation}/approve', [OperationViewController::class, 'approve'])
        ->middleware('permission:approve_operations')
        ->name('operations.approve');

    Route::post('/operations/{operation}/reject', [OperationViewController::class, 'reject'])
        ->middleware('permission:approve_operations')
        ->name('operations.reject');


    /*
    |--------------------------------------------------------------------------
    | Transactions
    |--------------------------------------------------------------------------
    */

    Route::get('/transactions', [TransactionViewController::class, 'index'])
        ->middleware('permission:view_transactions')
        ->name('transactions.index');


    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */

    Route::resource('customers', CustomerViewController::class)
        ->only([
            'index',
            'create',
            'store',
            'edit',
            'update',
            'destroy'
        ])
        ->middleware('permission:manage_customers');


    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        Route::get('/users', [UserViewController::class, 'index'])
            ->middleware('permission:manage_users')
            ->name('users.index');

        Route::get('/users/create', [UserViewController::class, 'create'])
            ->middleware('permission:manage_users')
            ->name('users.create');

        Route::post('/users', [UserViewController::class, 'store'])
            ->middleware('permission:manage_users')
            ->name('users.store');

        Route::get('/users/{user}/edit', [UserViewController::class, 'edit'])
            ->middleware('permission:manage_users')
            ->name('users.edit');

        Route::put('/users/{user}', [UserViewController::class, 'update'])
            ->middleware('permission:manage_users')
            ->name('users.update');

        Route::post('/users/{user}/approve', [UserViewController::class, 'approve'])
            ->middleware('permission:manage_users')
            ->name('users.approve');

        Route::post('/users/{user}/deactivate', [UserViewController::class, 'deactivate'])
            ->middleware('permission:manage_users')
            ->name('users.deactivate');

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        Route::get('/roles', [RoleViewController::class, 'index'])
            ->middleware('permission:manage_roles')
            ->name('roles.index');


        Route::get('/roles/create', [RoleViewController::class, 'create'])
            ->middleware('permission:manage_roles')
            ->name('roles.create');


        Route::post('/roles', [RoleViewController::class, 'store'])
            ->middleware('permission:manage_roles')
            ->name('roles.store');


        Route::get('/roles/{role}/edit', [RoleViewController::class, 'edit'])
            ->middleware('permission:manage_roles')
            ->name('roles.edit');


        Route::put('/roles/{role}', [RoleViewController::class, 'update'])
            ->middleware('permission:manage_roles')
            ->name('roles.update');


});


require __DIR__ . '/auth.php';
