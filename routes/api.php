<?php

use App\Http\Controllers\XenditController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/payment', [XenditController::class, 'createInvoice']);
Route::post('/payment/webhook/xendit', [XenditController::class, 'webHook']);
Route::post('/payment/create/virtualaccount', [XenditController::class, 'createVirtualAccount']);
Route::post('/payment/webhook/xendit/va', [XenditController::class, 'vituralAcountHook']);

Route::post('/register/user', [\App\Http\Controllers\RegisterController::class, 'register'])->name('register');
Route::post('/login/user', [\App\Http\Controllers\UserLoginController::class, 'login'])->name('login');


Route::group(['middleware' => ['api']], function () {
    Route::get('/test',[\App\Http\Controllers\UserLoginController::class, 'testAcces']);
    Route::post('/logout',[\App\Http\Controllers\UserLoginController::class, 'logout']);
});



