<?php

namespace App\Filament\Resources\Panel\CollectionResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\Panel\CollectionResource;
use Filament\Resources\RelationManagers\RelationManager;

/**
 * Relation manager for managing products attached to a collection.
 *
 * This relation manager uses the "products" relationship defined on the Collection model.
 * It supports attaching products via the collection_product pivot table and editing/detaching them.
 *
 * @package App\Filament\Resources\Panel\CollectionResource\RelationManagers
 */
class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Build the form used to attach/edit the relation.
     *
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(['default' => 1])
                ->schema([
                    // The product selection from available products.
                    // Note: Adjust the translation key if needed.
                    Select::make('product_id')
                        ->label('Produktnavn')
                        ->required()
                        ->multiple() // Allow selecting multiple products if desired
                        ->searchable()
                        ->preload()
                        ->native(false),
                ]),
        ]);
    }

    /**
     * Build the table used to display the attached products.
     *
     * @param \Filament\Tables\Table $table
     * @return \Filament\Tables\Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // Display the product name. Adjust the field as necessary if you would like to show an ID or other attribute.
                TextColumn::make('name')
                    ->label('Produkt')
                    ->searchable()
                ->sortable(),

                TextColumn::make('price')
                    ->label('Pris')
                    ->formatStateUsing(function ($state, $record): string {
                        // If the order is in NOK, show as "kr {amount},-" else use currency symbols.
                        if ($record->currency === 'nok' || $record->currency === null) {
                            return 'kr ' . number_format(((float)$state) / 100, 0) . ',-';
                        }
                        $currencySymbols = [
                            'USD' => '$',
                            'EUR' => '€',
                            'GBP' => '£',
                        ];
                        $symbol = $currencySymbols[$record->currency] ?? $record->currency . ' ';
                        return $symbol . number_format(((float)$state) / 100, 2);
                    })
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(
                        fn (Tables\Actions\AttachAction $action): array => [
                            $action->getRecordSelect(),
                            Select::make('product_id')
                                ->label(__('crud.collectionProduct.inputs.product_id.label'))
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
