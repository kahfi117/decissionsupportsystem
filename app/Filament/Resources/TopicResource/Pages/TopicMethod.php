<?php

namespace App\Filament\Resources\TopicResource\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\Resources\TopicResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class TopicMethod extends Page
{
    use InteractsWithRecord;
    protected static string $resource = TopicResource::class;
    protected static string $view = 'filament.resources.topic-resource.pages.topic-method';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
}
