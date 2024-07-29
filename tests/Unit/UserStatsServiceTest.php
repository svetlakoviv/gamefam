<?php

namespace Tests\Unit;

use App\Services\UserStatsService;
use GuzzleHttp\Client;
use Mockery;
use Tests\TestCase;

class UserStatsServiceTest extends TestCase
{
    protected UserStatsService $userStatsService;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a mock for Guzzle Client
        $mockGuzzleClient = Mockery::mock(Client::class);

        // Pass the mocked Guzzle client to the UserStatsService
        $this->userStatsService = new UserStatsService($mockGuzzleClient);
    }

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

        $result = $this->userStatsService->getTableData();

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

        $result = $this->userStatsService->getTableData();

        // Assert the number of results does not exceed MAX_DAYS
        $this->assertLessThanOrEqual(UserStatsService::MAX_DAYS, count($result));
    }

    public function testGenerateCSVWithDateRange()
    {
        $mockedQuery = Mockery::mock('alias:App\Models\UserStats');
        $mockedQuery->shouldReceive('query')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->andReturn(collect([
                ['created_at' => '2024-07-25', 'online_users' => 5],
                ['created_at' => '2024-07-26', 'online_users' => 10],
            ]));

        $from = '2024-07-25';
        $to = '2024-07-26';

        // Act: Call the method
        $csvData = $this->userStatsService->generateCSV($from, $to);

        // Assert: Check the CSV output
        $expectedCsvData = "Date,Online Users\n2024-07-25,5\n2024-07-26,10\n";
        $this->assertEquals($expectedCsvData, $csvData);
    }

    public function testGenerateCSVWithoutDateRange()
    {
        $mockedQuery = Mockery::mock('alias:App\Models\UserStats');
        $mockedQuery->shouldReceive('query')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->andReturn(collect([
                ['created_at' => now()->subDay()->format('Y-m-d'), 'online_users' => 3],
            ]));

        // Act: Call the method without parameters
        $csvData = $this->userStatsService->generateCSV();

        // Assert: Check the CSV output (it should include only the last day's data)
        $expectedCsvData = "Date,Online Users\n" . now()->subDay()->format('Y-m-d') . ",3\n";
        $this->assertEquals($expectedCsvData, $csvData);
    }
}
