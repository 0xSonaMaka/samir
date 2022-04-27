<?php

namespace App\Commander;

use App\Models\Commander;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait InteractsWithCommanders
{
    public function createCommander(
        string $name,
        float $fund,
        int $risk,
        int $botId = 0,
        $status = Commander::STATUS_CHILL
    ): Model|Builder
    {
        return Commander::query()->create([
            'name' => $name,
            'fund' => $fund,
            'risk' => $risk,
            'bot_id' => $botId,
            'status' => $status
        ]);
    }

    public function findOrCreateCommander(
        string $name,
        float $fund,
        int $risk,
        int $botId = 0
    ): Model|Builder
    {
        $commander = Commander::query()
            ->where('name', $name)
            ->where('fund', $fund)
            ->where('risk', $risk)
            ->where('bot_id', $botId)
            ->first();

        return $commander ?? $this->createCommander($name, $fund, $risk, $botId);
    }

    public function findCommanderOrFail(int $id): Model|Collection|Builder|array|null
    {
        return Commander::query()->findOrFail($id);
    }
}
