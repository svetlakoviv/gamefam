<?php

namespace Tests\Unit;

use App\Services\UserStatsService;
use GuzzleHttp\Client;
use Mockery;
use Tests\TestCase;

class UserStatsServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * get_table normal case
     */
    public function test_get_table_data(): void
    {
        // Mock the UserStats model
        $mock = Mockery::mock('alias:App\Models\UserStats');
        $mock->shouldReceive('query->select->groupBy->orderBy->get')
            ->andReturn(collect([
                (object)['date' => '2023-01-01', 'max_users' => 10, 'avg_users' => 5],
                (object)['date' => '2023-01-02', 'max_users' => 20, 'avg_users' => 15],
            ]));

        // Create an instance of the service
        $service = new UserStatsService(new Client());

        // Call the method and get the result
        $result = $service->getTableData();

        // Assert the result
        $expected = [
            '2023-01-01' => ['max' => 10, 'avg' => 5],
            '2023-01-02' => ['max' => 20, 'avg' => 15],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * get_table with more rows than days limit
     *
     * @return void
     */
    public function test_max_days_limit()
    {
        // Mock the UserStats model
        $mock = Mockery::mock('alias:App\Models\UserStats');
        $mock->shouldReceive('query->select->groupBy->orderBy->get')
            ->andReturn(collect([
                (object)['date' => '2023-01-01', 'max_users' => 10, 'avg_users' => 1],
                (object)['date' => '2023-01-02', 'max_users' => 20, 'avg_users' => 2],
                (object)['date' => '2023-01-02', 'max_users' => 20, 'avg_users' => 3],
                (object)['date' => '2023-01-02', 'max_users' => 20, 'avg_users' => 4],
                (object)['date' => '2023-01-02', 'max_users' => 20, 'avg_users' => 5],
                (object)['date' => '2023-01-02', 'max_users' => 20, 'avg_users' => 6],
                (object)['date' => '2023-01-02', 'max_users' => 20, 'avg_users' => 7],
                (object)['date' => '2023-01-02', 'max_users' => 20, 'avg_users' => 8],
                (object)['date' => '2023-01-02', 'max_users' => 20, 'avg_users' => 9],
                (object)['date' => '2023-01-02', 'max_users' => 20, 'avg_users' => 10],
            ]));

        // Create an instance of the service
        $service = new UserStatsService(new Client());

        // Call the method and get the result
        $result = $service->getTableData();

        // Assert the number of results does not exceed MAX_DAYS
        $this->assertLessThanOrEqual(UserStatsService::MAX_DAYS, count($result));
    }
}
