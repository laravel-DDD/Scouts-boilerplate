<?php

use App\Http\Controllers\Front\DashboardController;
use App\Http\Controllers\Front\Settings\DeleteController;
use App\Http\Controllers\Front\Settings\SecurityController;
use App\Http\Controllers\Front\Settings\TokensController;
use App\Http\Controllers\Front\Teams\MembersController;
use App\Http\Controllers\Front\WelcomeController;
use App\Http\Controllers\Front\Teams\TeamsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', WelcomeController::class)->name('welcome');

Route::group(['middleware' => ['auth', 'forbid-banned-user']], static function () {
    Route::get('/Account-verwijderen', DeleteController::class)->name('account.delete');

    Route::group(['prefix' => 'account-instellngen'], static function (): void {
        Route::view('/informatie', 'auth.settings.information')->name('account.settings.information');
        Route::get('/beveiliging', [SecurityController::class, 'index'])->name('account.settings.security');
        Route::post('/remove-sessions', [SecurityController::class, 'destroy'])->name('account.delete-sessions');
    });

    // Personal access tokens routes
    if (config('boilerplate.features.api')) {
        Route::group(['prefix' => 'api'], static function (): void {
            Route::get('/tokens', [TokensController::class, 'index'])->name('api.tokens');
            Route::post('/tokens', [TokensController::class, 'store'])->name('api.tokens.store');
            Route::get('/token/revoke/{personalAccessToken}', [TokensController::class, 'delete'])->name('api.tokens.revoke');
        });
    }

    // User team routes
    if (config('boilerplate.features.teams')) {
        Route::group(['prefix' => 'teams'], static function (): void {
            Route::get('/{team}', [TeamsController::class, 'show'])->name('teams.show');

            // Team member routes
            Route::group(['middleware' => 'teamowner'], static function (): void {
                Route::get('/{team}/members', [MembersController::class, 'index'])->name('teams.members.index');
                Route::post('/{team}/members', [MembersController::class, 'store'])->name('teams.members.store');
            });
        });
    }

   Route::get('/home', DashboardController::class)->name('home');
});

