<?php

use App\Http\Controllers\Api\UserStatsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/stats/{from?}/{to?}', [UserStatsController::class, 'stats']);

Route::get('/latest', [UserStatsController::class, 'latest']);

Route::get('/table', [UserStatsController::class, 'table']);

Route::get('/export/{from?}/{to?}', [UserStatsController::class, 'exportToCSV']);
