<?php

namespace App\Filament\Resources\TopicResource\Pages;

use App\Models\Topic;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\TopicResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class DetailTopic extends Page
{
    use InteractsWithRecord;
    protected static string $resource = TopicResource::class;
    protected static string $view = 'filament.resources.topic-resource.pages.detail-topic';

    public function mount(int|string $slug): void
    {
        $this->record = $this->resolveRecord($slug);
    }
    public function getTitle(): string
    {
        return $this->record->name; // Retrieve the title dynamically
    }
}
