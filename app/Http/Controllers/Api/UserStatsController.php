<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserStats;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class UserStatsController extends Controller
{
    const TEN_MINUTES = 600;

    public function stats($from = null, $to = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = UserStats::query();

        if ($from) {
            $query->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }

        if ($to) {
            $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        if (!$from && !$to) {
            $query->where('created_at', '>=', Carbon::now()->subDay());
        }

        return $query->get();
    }

    public function latest()
    {
        return Cache::remember('last_row', static::TEN_MINUTES, function () {
            return UserStats::latest('created_at')->first();
        });
    }

    public function table(): array
    {
        $tableData = [];
        /**
         * "2024-07-24" => ['avg' => 1600, 'max' => '2100'],
         * "2024-07-25" => ['avg' => 1700, 'max' => '2200'],
         * "2024-07-26" => ['avg' => 1800, 'max' => '2300']
         */

        $data = UserStats::query()->select('date', DB::raw('max(online_users) as max_users'), DB::raw('avg(online_users) as avg_users'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        foreach ($data as $row) {
            $tableData[$row['date']] = ['max' => $row->max_users, 'avg' => floor($row->avg_users)];
        }

        return $tableData;
    }

    public function exportToCSV(): \Illuminate\Http\Response
    {
        $yesterday = Carbon::now()->subDays();
        $data = UserStats::where('created_at', '>=', $yesterday)->get()->toArray();

        $csvData = "Date,Online Users\n";
        foreach ($data as $values) {
            $csvData .= "{$values['created_at']},{$values['online_users']}\n";
        }

        $filename = 'data-' . time() . '.csv';
        $response = Response::make($csvData);
        $response->header('Content-Type', 'text/csv');
        $response->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
