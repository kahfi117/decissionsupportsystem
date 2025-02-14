<?php

namespace App\Filament\Resources\TopicResource\Pages;

use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\HtmlString;
use App\Infolists\Components\DssGuide;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\TopicResource;

class TopicView extends ViewRecord
{
    protected static string $resource = TopicResource::class;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
    public function getTitle(): string
    {
        return $this->record->name; // Retrieve the title dynamically
    }
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Card::make([
                    Infolists\Components\TextEntry::make('name')
                        ->label('Nama'),
                    Infolists\Components\TextEntry::make('slug')
                        ->label('Slug'),
                    DssGuide::make('dss')
                        ->label('DSS')
                        ->columnSpanFull()
                ])->columns(2)
            ]);
    }
}
