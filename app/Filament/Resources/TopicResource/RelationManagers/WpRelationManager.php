<?php

namespace App\Filament\Resources\TopicResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Rangking;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Http\Controllers\DssController;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class WpRelationManager extends RelationManager
{
    protected static string $relationship = 'rankings';
    protected static ?string $title = 'Wp';
    protected static ?string $icon = 'heroicon-o-numbered-list';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Rangking::whereHas('alternatif', function (Builder $query) {
                    $query->where('topic_id', $this->ownerRecord->id)
                        ->where('method_id', 1);
                })
            )
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
                    ->label('Rank'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('Hitung Topsis')
                    ->label('DSS WP')
                    ->action(function () {
                        try {
                            app(DssController::class)->wpCalculation($this->ownerRecord->id);

                        } catch (\DivisionByZeroError $divisionByZeroError) {
                            Notification::make()
                                ->title('Gagal melakukan perhitungan SAW')
                                ->body('Cak Kembali Di pemberian Score. lengkapi data anda') // Menampilkan pesan error
                                ->danger()
                                ->send();

                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Gagal melakukan perhitungan SAW')
                                ->body('Something Wrong, ReCheck Your data') // Menampilkan pesan error
                                ->danger()
                                ->send();
                        }
                    })
                    ->icon('heroicon-o-calculator')
                    ->color('success')
                    ->requiresConfirmation()
                    ->successNotificationTitle('Perhitungan WP berhasil!')
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
