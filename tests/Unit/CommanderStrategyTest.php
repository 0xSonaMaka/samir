<?php

namespace Tests\Unit;

use App\Commander\InteractsWithCommanders;
use App\Models\Commander;
use App\Commander\Strategy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommanderStrategy extends TestCase
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
            'Commander With Strategy',
            1000,
            1
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_it_can_create_a_new_strategy()
    {
        $strategySellTrend = Strategy::create([
            'resolution' => 240,
            'value_min' => 80,
            'value_max' => 100,
            'type' => 'trend',
            'side' => 'sell'
        ]);

        $strategyBuyTrend = Strategy::create([
            'resolution' => 240,
            'value_min' => 0,
            'value_max' => 20,
            'type' => 'trend',
            'side' => 'sell'
        ]);

        $strategySellTrigger = Strategy::create([
            'resolution' => 5,
            'value_min' => 90,
            'value_max' => 100,
            'type' => 'trigger',
            'side' => 'buy'
        ]);

        $strategyBuyTrigger = Strategy::create([
            'resolution' => 5,
            'value_min' => 0,
            'value_max' => 10,
            'type' => 'trigger',
            'side' => 'buy'
        ]);

        $this->commander
            ->setSellTrend($strategySellTrend)
            ->setBuyTrend($strategyBuyTrend)
            ->setSellTrigger($strategySellTrigger)
            ->setBuyTrigger($strategyBuyTrigger)
            ->save();
    }
}
