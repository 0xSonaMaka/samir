<?php

namespace App\Models;

use App\Commander\Position;
use App\Commander\Profit;
use App\Commander\Risk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Commander extends Model
{
    use HasFactory;

    protected $table = 'commanders';

    protected ?Risk $riskCalculator = null;

    protected ?Position $position = null;

    protected ?Profit $profit = null;

    // Statuses
    public const STATUS_CHILL = 0;
    public const STATUS_BUY = 1;
    public const STATUS_SELL = 2;

    protected $attributes = [
        'status' => self::STATUS_CHILL
    ];

    protected $fillable = [
        'name',
        'fund',
        'risk',
        'bot_id',
        'status'
    ];

    public function selling(): static
    {
        $this->status = self::STATUS_SELL;
        $this->save();

        return $this;
    }

    public function buying(): static
    {
        $this->status = self::STATUS_BUY;
        $this->save();

        return $this;
    }

    public function chilling(): static
    {
        $this->status = self::STATUS_CHILL;
        $this->save();

        return $this;
    }

    public function trades(): HasMany
    {
        return $this->hasMany(CommanderTrade::class);
    }

    public function getBaseOrderSizeAttribute(): float|int
    {
        return $this->risk * $this->fund / 100;
    }

    public function buy(float $entry = 0.0)
    {
        if (self::STATUS_BUY === $this->status) {
            //@TODO Do buying via bot service here
            $this->trades()->create([
                'side' => 'buy',
                'bot_id' => $this->bot_id,
                'amount' => $this->base_order_size,
                'entry' => $entry
            ]);
        }
    }

    public function getProfit(): Profit
    {
        if (!$this->profit) {
            $profit = new Profit;
            $profit->setCommander($this);
            $profit->calculate();

            $this->profit = $profit;
        }

        return $this->profit;
    }

    public function getRisk(): Risk
    {
        if (!$this->riskCalculator) {
            $risk = new Risk;
            $risk->setCommander($this);
            $risk->calculate();

            $this->riskCalculator = $risk;
        }

        return $this->riskCalculator;
    }

    public function getPosition(): Position
    {
        if (!$this->position) {
            $position = new Position;
            $position->setCommander($this);
            $position->calculate();

            $this->position = $position;
        }

        return $this->position;
    }

    public function sell(float $entry = 0.0)
    {
        if (self::STATUS_SELL === $this->status) {
            //@TODO Do selling via bot service here
            $this->trades()->create([
                'side' => 'sell',
                'bot_id' => $this->bot_id,
                'amount' => $this->base_order_size,
                'entry' => $entry
            ]);
        }
    }

    public function getSummaryTrades(): Collection
    {
        return DB::table(app(CommanderTrade::class)->getTable())
            ->select(
                'side',
                DB::raw('MIN(created_at) as from_date'),
                DB::raw('MAX(created_at) as to_date'),
                DB::raw('SUM(amount) as size'),
                DB::raw('AVG(entry) as entry')
            )
            ->where('commander_id', $this->id)
            ->whereNotNull('amount')
            ->groupBy('side')
            ->get();
    }
}
