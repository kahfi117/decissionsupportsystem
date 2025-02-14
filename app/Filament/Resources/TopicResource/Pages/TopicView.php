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
                    Infolists\Components\TextEntry::make('methods.id')
                        ->label('Metode DSS')
                        ->formatStateUsing(fn($state) => [
                            1 => 'Weighted Product (WP)',
                            2 => 'Simple Additive Weighting (SAW)',
                            3 => 'TOPSIS'
                        ][$state])
                        ->badge(),
                    Infolists\Components\TextEntry::make('description')
                        ->limit(100)
                        ->default('-')
                        ->tooltip(function (Infolists\Components\TextEntry $component): ?string {
                            $state = $component->getState();

                            if (strlen($state) <= $component->getCharacterLimit()) {
                                return null;
                            }

                            // Only render the tooltip if the entry contents exceeds the length limit.
                            return $state;
                        }),
                    DssGuide::make('dss')
                        ->label('DSS')
                        ->hiddenLabel()
                        ->columnSpanFull()
                ])->columns(2)
            ]);
    }
}
