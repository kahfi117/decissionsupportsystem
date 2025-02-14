<?php

namespace App\Filament\Resources\TopicResource\RelationManagers;

use App\Http\Controllers\DssController;
use App\Models\Rangking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class RankingsRelationManager extends RelationManager
{
    protected static string $relationship = 'rankings';

    protected static ?string $title = 'SAW';

    protected static ?string $icon = 'heroicon-o-numbered-list';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('score')
            ->columns([
                Tables\Columns\TextColumn::make('alternatif.name')
                    ->label('Nama Alternatif')
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Skor'),
                Tables\Columns\TextColumn::make('rank')
                    ->badge()
                    ->sortable()
                    ->color('success')
                    ->label('Skor'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('Hitung SAW')
                    ->label('Lakukan DSS SAW')
                    ->action(fn() => app(DssController::class)->sawCalculation($this->ownerRecord->id))
                    ->icon('heroicon-o-calculator')
                    ->color('success')
                    ->requiresConfirmation()
                    ->successNotificationTitle('Perhitungan SAW berhasil!')
            ])
            ->actions([

            ])
            ->bulkActions([

            ]);
    }


    protected static function getRanking($record)
    {
        // Ambil semua data, urutkan dari skor tertinggi ke terendah
        $rankedData = Rangking::orderByDesc('score')->pluck('id')->toArray();

        // Ambil posisi index + 1 sebagai ranking
        return array_search($record->id, $rankedData) + 1;
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
