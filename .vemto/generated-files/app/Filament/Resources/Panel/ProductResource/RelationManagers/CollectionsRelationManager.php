<?php

namespace App\Filament\Resources\Panel\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Panel\ProductResource;
use Filament\Resources\RelationManagers\RelationManager;

class CollectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'collections';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(['default' => 1])->schema([
                Select::make('collection_id')
                    ->label(
                        __('crud.collectionProduct.inputs.collection_id.label')
                    )
                    ->required()
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->native(false),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('collection_id')->label(
                    __('crud.collectionProduct.inputs.collection_id.label')
                ),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\AttachAction::make()->form(
                    fn(Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),

                        Select::make('collection_id')
                            ->label(
                                __(
                                    'crud.collectionProduct.inputs.collection_id.label'
                                )
                            )
                            ->required()
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ]
                ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
