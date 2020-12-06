<?php

use App\Http\Controllers\Kiosk\DashboardController;
use App\Http\Controllers\Kiosk\Users\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Kiosk Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth', 'kiosk']], static function (): void {
    Route::get('/', DashboardController::class)->name('kiosk.dashboard');


    Route::group(['prefix' => 'users'], static function (): void {
       Route::get('/{filter?}', [UsersController::class, 'index'])->name('kiosk.users.index');
       Route::get('/gebruiker/{user}', [UsersController::class, 'show'])->name('kiosk.users.show');
       Route::get('/gebruikers/wijzigen/{user}', [UsersController::class, 'edit'])->name('kiosk.users.edit');
       Route::patch('/gebruikers/wijzigen/{userEntity}', [UsersController::class, 'update'])->name('kiosk.users.update');
       Route::get('/gebruikers/verwijderen/{user}', [UsersController::class, 'destroy'])->name('kiosk.users.delete');
    });
});