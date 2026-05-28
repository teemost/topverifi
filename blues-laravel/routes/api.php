<?php
use App\Http\Controllers\Api\ResellerApiController;
use App\Http\Middleware\ResellerApiAuth;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(ResellerApiAuth::class)->group(function () {

    // Wallet
    Route::get('/balance', [ResellerApiController::class, 'balance']);

    // SMM Boosting
    Route::get('/smm/services',        [ResellerApiController::class, 'smmServices']);
    Route::post('/smm/order',          [ResellerApiController::class, 'smmOrder']);
    Route::get('/smm/order/{id}',      [ResellerApiController::class, 'smmOrderStatus']);

    // Virtual Numbers
    Route::get('/numbers/countries',   [ResellerApiController::class, 'numberCountries']);
    Route::get('/numbers/services',    [ResellerApiController::class, 'numberServices']);
    Route::post('/numbers/order',      [ResellerApiController::class, 'numberOrder']);
    Route::get('/numbers/{id}/sms',    [ResellerApiController::class, 'numberSms']);
    Route::delete('/numbers/{id}',     [ResellerApiController::class, 'numberCancel']);
});
