<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource;
use Filament\Resources\RelationManagers\RelationManager;


class OrderItemsRelationManager extends RelationManager
{

    protected static string $relationship = 'orderItems';

    protected static ?string $recordTitleAttribute = 'name';

    // Set your custom table label here.
    protected static ?string $title = 'Ordrelinjer';


    public function form(Form $form): Form
    {

        return $form->schema([
            Grid::make(['default' => 1])->schema([
                TextInput::make('product_id')
                    ->label(__('crud.orderItems.inputs.product_id.label'))
                    ->nullable()
                    ->numeric()
                    ->step(1)
                    ->autofocus(),

                TextInput::make('product_variant_id')
                    ->label(
                        __('crud.orderItems.inputs.product_variant_id.label')
                    )
                    ->nullable()
                    ->numeric()
                    ->step(1),

                TextInput::make('quantity')
                    ->label(__('crud.orderItems.inputs.quantity.label'))
                    ->required()
                    ->numeric()
                    ->step(1),

                TextInput::make('unit_price')
                    ->label(__('crud.orderItems.inputs.unit_price.label'))
                    ->required()
                    ->numeric()
                    ->step(1),

                TextInput::make('total_price')
                    ->label(__('crud.orderItems.inputs.total_price.label'))
                    ->required()
                    ->numeric()
                    ->step(1),

                TextInput::make('name')
                    ->label(__('crud.orderItems.inputs.name.label'))
                    ->nullable()
                    ->string(),


            ]),
        ]);
    }

    public function table(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('product.name'),


                TextColumn::make('quantity')->label('Antall'),

                TextColumn::make('unit_price')->label('Enhetspris'),

                TextColumn::make('total_price')->label('Pris'),

                TextColumn::make('name')->label('Stripe ID'),


            ])
            ->filters([

            ])
            ->headerActions([

                Tables\Actions\CreateAction::make(),
            ])
            ->actions([

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                ]),
            ]);
    }
}
