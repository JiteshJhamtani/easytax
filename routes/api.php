<?php

use App\Http\Controllers\Api\ExternalIntakeController;
use Illuminate\Support\Facades\Route;

Route::middleware('external.intake')->group(function () {
    Route::post('/v1/applications/external-intake', [ExternalIntakeController::class, 'store']);
    Route::post('/applications/external-intake', [ExternalIntakeController::class, 'store']);
});
