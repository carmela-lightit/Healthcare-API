<?php

declare(strict_types=1);

use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\Route;
use Lightit\Users\App\Controllers\{GetUserController, DeleteUserController, ListUserController, StoreUserController, UpdateUserController};
use Lightit\Authentication\App\Controllers\{
    LoginController,
    LogoutController,
    RefreshController
};
use Lightit\Doctors\App\Controllers\{
    GetDoctorController,
    ListDoctorController,
    StoreDoctorController,
    UpdateDoctorController,
    DeleteDoctorController
};
use Lightit\Clinics\App\Controllers\{
    GetClinicController,
    ListClinicController,
    StoreClinicController,
    UpdateClinicController,
    DeleteClinicController
};
use Lightit\Appointments\App\Controllers\{
    StoreAppointmentController,
    ListMyAppointmentsController,
    DeleteAppointmentController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')
    ->get('/me', fn(
        #[CurrentUser] $user
    ) => response()->json([
        'data' => $user,
    ]));

/*
|--------------------------------------------------------------------------
| Users Routes
|--------------------------------------------------------------------------
*/
Route::prefix('users')
    ->middleware([])
    ->group(static function (): void {
        Route::get('/', ListUserController::class);
        Route::post('/', StoreUserController::class);
        Route::prefix('{user}')
            ->group(static function (): void {
                Route::get('/', GetUserController::class)->withTrashed();
                Route::put('/', UpdateUserController::class);
                Route::delete('/', DeleteUserController::class);
            })->whereNumber('user');
    });

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(static function (): void {
    Route::post('login', LoginController::class);
    Route::middleware('auth:api')->group(static function (): void {
        Route::post('logout', LogoutController::class);
        Route::post('refresh', RefreshController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Doctors Routes
|--------------------------------------------------------------------------
*/
Route::prefix('doctors')
    ->group(static function (): void {
        Route::get('/', ListDoctorController::class);
        Route::post('/', StoreDoctorController::class);
        Route::prefix('{doctor}')
            ->group(static function (): void {
                Route::get('/', GetDoctorController::class);
                Route::put('/', UpdateDoctorController::class);
                Route::delete('/', DeleteDoctorController::class);
            })->whereNumber('doctor');
    });

/*
|--------------------------------------------------------------------------
| Clinics Routes
|--------------------------------------------------------------------------
*/
Route::prefix('clinics')
    ->group(static function (): void {
        Route::get('/', ListClinicController::class);
        Route::post('/', StoreClinicController::class);
        Route::prefix('{clinic}')
            ->group(static function (): void {
                Route::get('/', GetClinicController::class);
                Route::put('/', UpdateClinicController::class);
                Route::delete('/', DeleteClinicController::class);
            })->whereNumber('clinic');
    });

/*
|--------------------------------------------------------------------------
| Appointment Routes
|--------------------------------------------------------------------------
*/
Route::prefix('appointments')
    ->middleware('auth:api')
    ->group(static function (): void {
        Route::post('/', StoreAppointmentController::class);
        Route::get('/me', ListMyAppointmentsController::class);
        Route::delete('/{appointment}', DeleteAppointmentController::class)
            ->whereNumber('appointment');
    });
