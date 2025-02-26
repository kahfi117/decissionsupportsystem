<?php

namespace App\Filament\Resources\TopicResource\Pages;

use App\Models\Topic;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\TopicResource;
use App\Filament\Clusters\Topic as TopicCluster;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use App\Filament\Resources\TopicResource\RelationManagers;

class DetailTopic extends Page
{
    use InteractsWithRecord;
    protected static ?string $cluster = TopicCluster::class;
    protected static string $resource = TopicResource::class;
    protected static string $view = 'filament.resources.topic-resource.pages.detail-topic';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
    public function getTitle(): string
    {
        return $this->record->name; // Retrieve the title dynamically
    }

}
