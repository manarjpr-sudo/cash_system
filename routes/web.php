<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OperationViewController;
use App\Http\Controllers\CustomerViewController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');


Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::get('/operations/create', [OperationViewController::class, 'create'])
        ->name('operations.create');
    
    Route::post('/operations', [OperationViewController::class, 'store'])
    ->name('operations.store');

    Route::get('/operations', [OperationViewController::class, 'index'])
        ->name('operations.index');

    Route::post('/operations/{operation}/approve', [OperationViewController::class, 'approve'])
        ->name('operations.approve');

    Route::post('/operations/{operation}/reject', [OperationViewController::class, 'reject'])
        ->name('operations.reject');

    Route::resource('customers', CustomerViewController::class)
        ->only([
            'index',
            'create',
            'store',
            'edit',
            'update',
            'destroy'
        ]);
    
    


});


require __DIR__.'/auth.php';