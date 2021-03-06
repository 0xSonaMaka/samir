<?php

namespace Tests\Unit;

use App\Commander\InteractsWithCommanders;
use App\Models\Commander;
use App\Commander\Profit;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\InteractsWithTradingSeeds;

class CommanderProfitTest extends TestCase
{
    use DatabaseTransactions,
        InteractsWithCommanders,
        InteractsWithTradingSeeds;

    protected Commander $commander;

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    public function setUp(): void
    {
        parent::setUp();

        $this->commander = $this->createCommander(
            'Test Commander', // name
            1000, // fund
            1, // 1% risk,
            0 // 3Commas bot ID
        );

        $this->seedTradingRecords();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_it_can_calculate_profit_for_simple_trades()
    {
        $profit = $this->commander->getProfit();

        $this->assertInstanceOf(Profit::class, $profit);
        $this->assertEquals(30.0, $profit->getTotal());
        $this->assertEquals(0.1, $profit->getDailyProfitPercentage());
        $this->assertEquals(3, $profit->getMonthlyProfitPercentage());
        $this->assertEquals(36.5, $profit->getApy());
    }
}
