<?php

namespace App\Filament\Manufacturer\Widgets;

use App\Models\Workforce;
use App\Models\ProductionStage;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StageStatsWidget extends BaseWidget
{
    public string $stage; // passed from the Filament Page
    protected static ?string $pollingInterval = '10s';


    protected array $stageSequence = ['printing', 'packaging', 'delivery'];

    protected function getStats(): array
    {
        $currentStage = $this->stage;

        // 1️⃣ Completed jobs for current stage
        $completed = ProductionStage::where('stage', $currentStage)
            ->where('status', 'completed')
            ->count();

        // 2️⃣ Available workers in this stage (by job)
        $busyCurrent = ProductionStage::where('stage', $currentStage)
            ->where('status', 'in_progress')
            ->pluck('workforces_id');

        $availableCurrent = Workforce::where('job', $currentStage)
            ->whereNotIn('id', $busyCurrent)
            ->count();

        // 3️⃣ Available workers for next stage
        $index = array_search($currentStage, $this->stageSequence);
        $nextStage = $this->stageSequence[$index + 1] ?? null;

        $availableNext = null;

        if ($nextStage) {
            $busyNext = ProductionStage::where('stage', $nextStage)
                ->where('status', 'in_progress')
                ->pluck('workforces_id');

            $availableNext = Workforce::where('job', $nextStage)
                ->whereNotIn('id', $busyNext)
                ->count();
        }

        return array_filter([
            Stat::make("Completed " . ucfirst($currentStage), $completed)
                ->description("Jobs completed in $currentStage stage")
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make("Available " . ucfirst($currentStage) . " Workers", $availableCurrent)
                ->description("Free workers for $currentStage")
                ->color('info')
                ->icon('heroicon-o-user-group'),

            $nextStage
                ? Stat::make("Available " . ucfirst($nextStage) . " Workers", $availableNext)
                    ->description("Ready for $nextStage stage")
                    ->color('warning')
                    ->icon('heroicon-o-arrow-right-circle')
                : null,
        ]);
    }
}
