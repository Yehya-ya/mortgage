<?php

use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::prefix('loans')->group(function () {
    Route::post('/calculate', [LoanController::class, 'calculate']);
    Route::get('/{loan}/amortization-schedule', [LoanController::class, 'getAmortizationSchedule']);
    Route::get('/{loan}/extra-payment-schedule', [LoanController::class, 'getExtraPaymentSchedule']);
});