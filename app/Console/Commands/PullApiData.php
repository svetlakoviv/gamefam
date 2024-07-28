<?php

namespace App\Console\Commands;

use App\Services\UserStatsService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;

class PullApiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pull-api-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull the online users stats and put it into DB';

    /**
     * Execute the console command.
     *
     * @throws GuzzleException
     */
    public function handle(UserStatsService $service): int
    {
        //try to get information from the LINK
        $result = $service->loadUserStatsAndSaveToDB();

        //on success we save it to UserStats
        if (!empty($result)) {
            $this->info('Data pulled and inserted successfully.');
        } else {
            $this->error('Failed to pull data from API.');
        }

        return 0;
    }
}
