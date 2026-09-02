<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuditLogController;


/*
|--------------------------------------------------------------------------
| Public Authentication
|--------------------------------------------------------------------------
*/

Route::get('/registration-roles', [AuthController::class, 'registrationRoles'])
    ->name('api.registration-roles');

Route::post('/register', [AuthController::class, 'register'])
    ->name('api.register');

Route::post('/login', [AuthController::class, 'login'])
    ->name('api.login');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');


/*
|--------------------------------------------------------------------------
| Authenticated API
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('api.logout');

    Route::prefix('v1')
        ->name('api.')
        ->group(function () {

            // ====== مسارات المستخدمين ======
            Route::get('/roles', [UserController::class, 'roles'])
                ->middleware('permission:manage_users')
                ->name('roles');

            Route::get('/users', [UserController::class, 'index'])
                ->middleware('permission:manage_users')
                ->name('users.index');

            Route::get('/users/{user}', [UserController::class, 'show'])
                ->middleware('permission:manage_users')
                ->name('users.show');

            Route::post('/users/{user}/approve', [UserController::class, 'approve'])
                ->middleware('permission:manage_users')
                ->name('users.approve');

            Route::post('/users/{user}/reject', [UserController::class, 'reject'])
                ->middleware('permission:manage_users')
                ->name('users.reject');

            Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])
                ->middleware('permission:manage_users')
                ->name('users.deactivate');

            Route::post('/users/{user}/activate', [UserController::class, 'activate'])
                ->middleware('permission:manage_users')
                ->name('users.activate');

            // داخل Route::prefix('v1')->group

            // الموارد الأساسية (بدون تكرار)
            
            Route::apiResource('operations', OperationController::class)->middleware('permission:manage_operations');
            
            Route::apiResource('customers', CustomerController::class)->middleware('permission:manage_customers');
            
            Route::apiResource('approvals', ApprovalController::class)->middleware('permission:manage_approvals');
            
            Route::apiResource('categories', CategoryController::class)->only(['index', 'store'])->middleware('permission:manage_operations');   

            // ====== لوحة التحكم ======
            Route::get('/dashboard', [DashboardController::class, 'api'])
                ->name('dashboard');

            // ====== الملف الشخصي ======
            Route::get('/profile', [ProfileController::class, 'show'])
                ->middleware('auth:sanctum');

            Route::put('/profile', [ProfileController::class, 'updateProfile'])
                ->middleware('auth:sanctum');

            Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
                ->middleware('auth:sanctum');

            // ====== مسارات التصنيفات ======
            Route::get('/categories', [CategoryController::class, 'index'])
                ->middleware('permission:manage_operations');

            Route::post('/categories', [CategoryController::class, 'store'])
                ->middleware('permission:manage_operations');

            Route::put('/categories/{category}', [CategoryController::class, 'update'])
                ->middleware('permission:manage_operations');

            Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
                ->middleware('permission:manage_operations');

            // ====== الإعدادات ======
            Route::get('/settings', [SettingController::class, 'index'])
                ->middleware('auth:sanctum');

            Route::put('/settings', [SettingController::class, 'update'])
                ->middleware('auth:sanctum');

            // ====== إدارة الأدوار والصلاحيات ======
            Route::get('/permissions', [PermissionController::class, 'index'])
                ->middleware('permission:manage_users');

            Route::apiResource('roles', RoleController::class)
                ->middleware('permission:manage_users');

            // ====== الإشعارات ======
            Route::get('/notifications', [NotificationController::class, 'index'])
                ->middleware('auth:sanctum');

            Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])
                ->middleware('auth:sanctum');

            Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
                ->middleware('auth:sanctum');

            Route::put('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
                ->middleware('auth:sanctum');

            // ====== النسخ الاحتياطي ======
            Route::post('/backup', function () {
                if (!auth()->user()->hasPermission('manage_users')) {
                    return response()->json(['message' => 'Unauthorized'], 403);
                }
                try {
                    \Artisan::call('backup:run');
                    return response()->json(['message' => 'Backup created successfully']);
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Backup failed: ' . $e->getMessage()], 500);
                }
            })->middleware('auth:sanctum');

            Route::post('/approvals', [App\Http\Controllers\ApprovalController::class, 'store'])
                ->middleware('auth:sanctum')
                ->middleware('permission:manage_approvals');

        }); // نهاية v1

}); // نهاية auth:sanctum