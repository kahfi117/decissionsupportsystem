<?php

namespace App\Filament\Resources\TopicResource\RelationManagers;

use Throwable;
use Filament\Forms;
use Filament\Tables;
use App\Models\Rangking;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use App\Http\Controllers\DssController;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class RankingsRelationManager extends RelationManager
{
    protected static string $relationship = 'rankings';

    protected static ?string $title = 'SAW';

    protected static ?string $icon = 'heroicon-o-numbered-list';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Rangking::whereHas('alternatif', function (Builder $query) {
                    $query->where('topic_id', $this->ownerRecord->id)
                        ->where('method_id', 2);
                })
            )
            ->recordTitleAttribute('score')
            ->columns([
                Tables\Columns\TextColumn::make('alternatif.name')
                    ->label('Nama Alternatif')
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->formatStateUsing(fn(float $state): float => round($state, 3))
                    ->label('Skor'),
                Tables\Columns\TextColumn::make('rank')
                    ->badge()
                    ->sortable()
                    ->color('success')
                    ->label('Rank'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('Hitung SAW')
                    ->label('DSS SAW')
                    ->action(function () {
                        try {
                            app(DssController::class)->sawCalculation($this->ownerRecord->id);

                        } catch (\DivisionByZeroError $divisionByZeroError) {
                            Notification::make()
                                ->title('Gagal melakukan perhitungan SAW')
                                ->body('Cak Kembali Di pemberian Score. lengkapi data anda') // Menampilkan pesan error
                                ->danger()
                                ->send();

                        } catch (Throwable $e) {
                            Notification::make()
                                ->title('Gagal melakukan perhitungan SAW')
                                ->body($e->getMessage()) // Menampilkan pesan error
                                ->danger()
                                ->send();
                        }
                    })
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
