<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserStatsService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class UserStatsController extends Controller
{
    const TEN_MINUTES = 600;

    public function stats(UserStatsService $service, $from = null, $to = null): Collection
    {
        return Cache::remember('stats_' . $from . $to, static::TEN_MINUTES, function () use ($service, $from, $to) {
            return $service->getUserStatsFromDB($from, $to);
        });
    }

    public function latest(UserStatsService $service)
    {
        //if we already have it cached, then return this instead
        return Cache::remember('online_users', static::TEN_MINUTES, function () use ($service) {
            return $service->loadUserStats();
        });
    }

    public function table(UserStatsService $service): array
    {
        return Cache::remember('table_data', static::TEN_MINUTES, function () use ($service) {
            return $service->getTableData();
        });
    }

    public function exportToCSV(UserStatsService $service, $from = null, $to = null): \Illuminate\Http\Response
    {
        //NOTE: we don't cache the csv generation!
        $csv = $service->generateCSV($from, $to);

        //create file for download
        $filename = 'data-' . time() . '.csv';
        $response = Response::make($csv);
        $response->header('Content-Type', 'text/csv');
        $response->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
