<?php

namespace App\Filament\Widgets;

use App\Models\Topic;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TopicOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $countTopic = Topic::count();
        return [
            Stat::make('Topik', $countTopic)
                ->description('Total Topik yang telah dibuat')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before),
        ];
    }
}
