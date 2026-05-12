<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard-kpis', [\App\Http\Controllers\Api\KpiController::class, 'getKpis']);