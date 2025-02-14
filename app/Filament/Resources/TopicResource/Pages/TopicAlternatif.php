<?php

namespace App\Filament\Resources\TopicResource\Pages;

use App\Filament\Resources\TopicResource;
use App\Filament\Resources\TopicResource\RelationManagers\AlternatifsRelationManager;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class TopicAlternatif extends ViewRecord
{
    protected static string $resource = TopicResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $breadcrumb = 'Alternatif';

    public function getTitle(): string
    {
        return "Alternatif {$this->record->name}"; // Retrieve the title dynamically
    }

    public function getRelationManagers(): array
    {
        return [
            AlternatifsRelationManager::class,
        ];
    }
}
