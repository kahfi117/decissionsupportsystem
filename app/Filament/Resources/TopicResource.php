<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Topic;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\TopicMethod;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TopicResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TopicResource\RelationManagers;

class TopicResource extends Resource
{
    protected static ?string $model = Topic::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    })
                    ->required()
                    ->maxLength(255)
                    ->hiddenOn('view'),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->hiddenOn('view'),
                Forms\Components\CheckboxList::make('methods')
                    ->relationship('methods')
                    ->label('Pilih Metode Perankingan')
                    ->options([
                        1 => 'Weighted Product (WP)',
                        2 => 'Simple Additive Weighting (SAW)',
                        3 => 'TOPSIS'
                    ])
                    ->columnSpanFull()
                    ->columns(3)
                    ->hiddenOn('view')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('methods.id')
                    ->badge()
                    ->label('Metode DSS')
                    ->formatStateUsing(fn($state) => [
                        1 => 'Weighted Product (WP)',
                        2 => 'Simple Additive Weighting (SAW)',
                        3 => 'TOPSIS'
                    ][$state])
                    ->markdown()

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('dss')
                    ->label('DSS')
                    ->color('success')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->url(fn($record) => route(
                        'filament.panel.resources.topics.dss',
                        ['record' => $record]
                    )),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\CategoriesRelationManager::class,
            // RelationManagers\AlternatifsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTopics::route('/'),
            // 'create' => Pages\CreateTopic::route('/create'),
            // 'edit' => Pages\EditTopic::route('/{record}/edit'),
            'detail' => Pages\DetailTopic::route('/detail/{record}'),
            'dss' => Pages\TopicView::route('/dss/{record}'),
            'category' => Pages\TopicCategories::route('/dss/{record}/category'),
            'alternatif' => Pages\TopicAlternatif::route('/dss/{record}/alternatif'),
            'alternatif.score' => Pages\TopicAlternatifScore::route('/dss/{record}/alternatif/score'),
            'method' => Pages\TopicMethod::route('/dss/{record}/method'),
            'rangking' => Pages\TopicRanking::route('/dss/{record}/rangking'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\TopicView::class,
            Pages\TopicCategories::class,
            Pages\TopicAlternatif::class,
            Pages\TopicAlternatifScore::class,
            Pages\TopicRanking::class
        ]);
    }
}
