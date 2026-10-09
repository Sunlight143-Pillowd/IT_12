<?php

namespace App\Console\Commands;

use App\Models\PcBuild;
use App\Services\PcBuildService;
use Illuminate\Console\Command;

class ExpirePcBuildReservations extends Command
{
    protected $signature = 'pcbuild:expire';

    protected $description = 'Release reservations for PC builds that have expired.';

    public function handle(PcBuildService $pcBuildService): int
    {
        $expiredBuilds = PcBuild::query()
            ->where('status', 'reserved')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expiredBuilds as $build) {
            $pcBuildService->expire($build);
            $this->info('Expired reservation for ' . $build->build_number);
        }

        return self::SUCCESS;
    }
}
