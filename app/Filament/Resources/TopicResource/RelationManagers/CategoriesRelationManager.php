<?php

namespace App\Filament\Resources\TopicResource\RelationManagers;

use App\Rules\WeightsKriteria;
use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Guava\FilamentIconSelectColumn\Tables\Columns\IconSelectColumn;

class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Kategori')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('parent_id')
                    ->label('Kategori induk')
                    ->native(false)
                    ->preload()
                    ->options(fn($record): array =>
                        Category::where('id', '!=', $record)
                            ->where('topic_id', '=', $this->ownerRecord->id)
                            ->pluck('name', 'id')->toArray()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('parent.name')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        null => 'danger',
                        default => 'success',
                    })
                    ->formatStateUsing(fn(string $state): ?string => $state != '' ? $state : 'Induk'),
                Tables\Columns\TextInputColumn::make('name')
                    ->rules(['required', 'max:255']),
                Tables\Columns\TextInputColumn::make('weight')
                    ->label('Bobot')
                    ->rules(fn($record): array => [
                        'required',
                        'numeric',
                        new WeightsKriteria(
                            $record->parent_id ?? null,
                            $record->id ?? null,
                            $this->ownerRecord->id ?? null,

                        )
                    ]),
                Tables\Columns\SelectColumn::make('cat')
                    ->label('Kategori')
                    ->options([
                        true => 'Benefit',
                        false => 'Cost',
                    ])
                    ->selectablePlaceholder(false),
                IconSelectColumn::make('is_active')
                    ->label('Aktif/NonAktif')
                    ->options([
                        true => 'Aktif',
                        false => 'Non-Aktif',
                    ])
                    ->icons([
                        true => 'heroicon-o-check-circle',
                        false => 'heroicon-o-x-circle',
                    ])
                    ->colors([
                        true => 'success',
                        false => 'danger'
                    ])
                    ->closeOnSelection()
                    ->beforeStateUpdated(function ($record, $state) {
                        // Jika mencoba mengaktifkan produk anak, periksa apakah parent aktif
                        if ($state && $record->parent_id) {
                            $parent = Category::find($record->parent_id);
                            if ($parent && !$parent->is_active) {
                                Notification::make()
                                    ->title('Gagal Mengaktifkan')
                                    ->body('Produk ini memiliki Induk yang dinonaktifkan. Harap aktifkan Induk terlebih dahulu.')
                                    ->danger()
                                    ->send();
                                Category::where('id', $record->id)->update(['is_active' => false, 'weight' => 0]);
                            }
                        }

                        // Jika parent dinonaktifkan, semua child juga harus nonaktif
                        if (!$state) {
                            Category::where('parent_id', $record->id)->update(['is_active' => false, 'weight' => 0]);

                            if ($record->parent_id) {
                                Notification::make()
                                    ->title('Menonaktifkan Semua Sub Kategori')
                                    ->body('Aksi ini menyebabkan semua sub kategori juga akan dinonaktifkan')
                                    ->success()
                                    ->send();
                            }
                        }
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state && $record->parent_id) {
                            $parent = Category::find($record->parent_id);
                            if ($parent && !$parent->is_active) {
                                Category::where('id', $record->id)->update(['is_active' => false, 'weight' => 0.0]);
                            }
                        }
                    }),
            ])
            ->filters([
                //
            ])
            ->defaultSort('_lft', 'asc')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([

            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
