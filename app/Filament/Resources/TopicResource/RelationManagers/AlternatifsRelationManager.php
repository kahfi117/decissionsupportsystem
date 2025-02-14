<?php

namespace App\Filament\Resources\TopicResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Alternatif;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class AlternatifsRelationManager extends RelationManager
{
    protected static string $relationship = 'alternatifs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextInputColumn::make('code'),
                Tables\Columns\TextInputColumn::make('name'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\Action::make('tambahkan')
                    ->label('Tambahkan')
                    ->action(fn() => Alternatif::create([
                        'name' => '',
                        'topic_id' => $this->ownerRecord->id,
                        'code' => 'A' . Alternatif::where('topic_id', $this->ownerRecord->id)
                            ->count() + 1
                    ]))
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->modalHeading('') // Menghilangkan judul modal
                    ->modalDescription('') // Menghilangkan deskripsi modal
                    ->successNotificationTitle('Data berhasil dihapus')
                    ->requiresConfirmation(false),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
