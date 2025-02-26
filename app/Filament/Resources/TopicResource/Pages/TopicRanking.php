<?php

namespace App\Filament\Resources\TopicResource\Pages;

use App\Models\Topic;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\TopicResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TopicResource\RelationManagers\WpRelationManager;
use App\Filament\Resources\TopicResource\RelationManagers\TopsisRelationManager;
use App\Filament\Resources\TopicResource\RelationManagers\RankingsRelationManager;
use App\Filament\Resources\TopicResource\RelationManagers\CategoriesRelationManager;

class TopicRanking extends ViewRecord
{
    protected static string $resource = TopicResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-numbered-list';

    protected static ?string $breadcrumb = 'Ranking';
    protected static ?string $title = 'Ranking';

    public function getTitle(): string
    {
        return "Ranking {$this->record->name}"; // Retrieve the title dynamically
    }

    public function getRelationManagers(): array
    {

        $relationManagers = [];

        // Ambil topic dengan relasi methods
        $topic = Topic::with('methods')->find($this->record->id);

        if (!$topic) {
            return [];
        }

        // Ambil daftar ID methods yang terkait dengan topic
        $methodIds = $topic->methods->pluck('id')->toArray();

        // Cek apakah ada id 1 (WP)
        if (in_array(1, $methodIds)) {
            $relationManagers[] = WpRelationManager::class;
        }
        // Cek apakah ada id 1 (WP)
        if (in_array(2, $methodIds)) {
            $relationManagers[] = RankingsRelationManager::class;
        }

        // Cek apakah ada id 2 (TOPSIS)
        if (in_array(3, $methodIds)) {
            $relationManagers[] = TopsisRelationManager::class;
        }

        return $relationManagers;
    }
}
