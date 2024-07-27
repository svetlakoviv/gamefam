<?php

namespace App\Console\Commands;

use App\Models\UserStats;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class PullApiData extends Command
{
    const LINK = 'https://origins.habbo.com/api/public/origins/users';
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
     */
    public function handle(Client $client, UserStats $userStats)
    {
        //try to get information from the LINK
        $response = $client->get(static::LINK);

        //on success we save it to UserStats
        //dd($response->getStatusCode());

        if ($response->getStatusCode() === 200) {
            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);
            //dd($data);
            $onlineUsers = $data['onlineUsers'];
            // Process and insert data into the database

            $userStats = new UserStats();
            $userStats->online_users = $onlineUsers;
            $userStats->save();


            $this->info('Data pulled and inserted successfully.');
        } else {
            $this->error('Failed to pull data from API.');
        }

        return 0;
    }
}
