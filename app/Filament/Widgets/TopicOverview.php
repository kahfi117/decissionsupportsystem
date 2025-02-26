<?php

namespace App\Filament\Widgets;

use App\Models\Topic;
use App\Models\TopicMethod;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TopicOverview extends BaseWidget
{
    protected function getColumns(): int
    {
        $count = count($this->getCachedStats());

        if ($count < 2) {
            return 2;
        }

        if (($count % 2) !== 1) {
            return 2;
        }

        return 2;
    }

    protected function getStats(): array
    {
        $wp = TopicMethod::where('method_id', 1)->count();
        $saw = TopicMethod::where('method_id', 2)->count();
        $topsis = TopicMethod::where('method_id', 3)->count();

        $countTopic = Topic::count();
        return [
            Stat::make('Topik', $countTopic)
                ->description('Total Topik yang telah dibuat')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color(color: 'success'),
            Stat::make('WP Method', $wp)
                ->description('Total Topik yang Menggunakan Metode WP')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('SAW Method', $saw)
                ->description('Total Topik yang Menggunakan Metode SAW')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('TOPSIS Method', $topsis)
                ->description('Total Topik yang Menggunakan Metode Topsis')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

        ];
    }
}
