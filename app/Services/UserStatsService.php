<?php

namespace App\Services;

use App\Models\UserStats;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserStatsService
{
    const LINK = 'https://origins.habbo.com/api/public/origins/users';
    const MAX_DAYS = 7;
    private Client $client;

    public function __construct(Client $client){
        $this->client = $client;
    }

    /**
     * @throws GuzzleException
     */
    public function loadUserStats(): ?int
    {
        $return = null;
        $response = $this->client->get(static::LINK);

        //on success we save it to UserStats
        if ($response->getStatusCode() === 200) {
            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);
            $onlineUsers = $data['onlineUsers'];

            $return = $onlineUsers;
        }

        return $return;
    }

    /**
     * @throws GuzzleException
     */
    public function loadUserStatsAndSaveToDB(): ?int
    {
        $onlineUsers = $this->loadUserStats();
        if(!empty($onlineUsers)){
            $this->saveUserStatsToDB($onlineUsers);
        }
        return $onlineUsers;
    }

    private function saveUserStatsToDB($onlineUsers): void
    {
        $date = Carbon::now()->format('Y-m-d');
        $userStats = new UserStats();
        $userStats->online_users = $onlineUsers;
        $userStats->date = $date;
        $userStats->save();
    }

    public function getUserStatsFromDB($from = null, $to = null): Collection
    {
        $query = UserStats::query();
        $query = $this->applyTimeWindow($query, $from, $to);

        return $query->get();
    }

    private function applyTimeWindow($query, $from, $to){
        if ($from) {
            $query->where('created_at', '>=', \Carbon\Carbon::parse($from)->startOfDay());
        }

        if ($to) {
            $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        if (!$from && !$to) {
            $query->where('created_at', '>=', Carbon::now()->subDay());
        }

        return $query;
    }

    public function getTableData(): array
    {
        $tableData = [];
        $data = UserStats::query()->select('date', DB::raw('max(online_users) as max_users'), DB::raw('avg(online_users) as avg_users'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $dayCounter = 0;
        foreach ($data as $row) {
            $dayCounter++;
            if($dayCounter > static::MAX_DAYS) {
                break;
            }
            $tableData[$row['date']] = ['max' => $row->max_users, 'avg' => floor($row->avg_users)];
        }

        return $tableData;
    }

    public function generateCSV($from = null, $to = null): string
    {
        $query = UserStats::query();
        $query = $this->applyTimeWindow($query, $from, $to);
        $data = $query->get()->toArray();

        $csvData = "Date,Online Users\n";
        foreach ($data as $values) {
            $csvData .= "{$values['created_at']},{$values['online_users']}\n";
        }

        return $csvData;
    }
}
