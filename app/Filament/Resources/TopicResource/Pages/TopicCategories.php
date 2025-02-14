<?php

namespace App\Filament\Resources\TopicResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\TopicResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TopicResource\RelationManagers\CategoriesRelationManager;

class TopicCategories extends ViewRecord
{
    protected static string $resource = TopicResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public function getRelationManagers(): array
    {
        return [
            CategoriesRelationManager::class,
        ];
    }
}
