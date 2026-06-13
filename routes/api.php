<?php

use App\Http\Controllers\Api\KpiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->get('/dashboard-kpis', [KpiController::class, 'getKpis']);
