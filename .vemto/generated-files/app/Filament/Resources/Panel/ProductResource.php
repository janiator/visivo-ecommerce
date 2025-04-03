<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\ProductResource\Pages;
use App\Filament\Resources\Panel\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.products.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.products.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.products.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('store_id')
                        ->label('Store')
                        ->required()
                        ->relationship('store', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('status')
                        ->label(__('crud.products.inputs.status.label'))
                        ->required()
                        ->string(),

                    TextInput::make('name')
                        ->label(__('crud.products.inputs.name.label'))
                        ->required()
                        ->string(),

                    TextInput::make('type')
                        ->label(__('crud.products.inputs.type.label'))
                        ->nullable()
                        ->string(),

                    RichEditor::make('description')
                        ->label(__('crud.products.inputs.description.label'))
                        ->nullable()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    TextInput::make('price')
                        ->label(__('crud.products.inputs.price.label'))
                        ->nullable()
                        ->numeric()
                        ->step(1),

                    RichEditor::make('short_description')
                        ->label(
                            __('crud.products.inputs.short_description.label')
                        )
                        ->nullable()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    TextInput::make('stripe_product_id')
                        ->label(
                            __('crud.products.inputs.stripe_product_id.label')
                        )
                        ->nullable()
                        ->string(),

                    DateTimePicker::make('deleted_at')
                        ->label(__('crud.products.inputs.deleted_at.label'))
                        ->rules(['date'])
                        ->nullable()
                        ->native(false),

                    RichEditor::make('metadata')
                        ->label(__('crud.products.inputs.metadata.label'))
                        ->nullable()
                        ->string()
                        ->fileAttachmentsVisibility('public'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('store.name')->label('Store'),

                TextColumn::make('status')->label(
                    __('crud.products.inputs.status.label')
                ),

                TextColumn::make('name')->label(
                    __('crud.products.inputs.name.label')
                ),

                TextColumn::make('type')->label(
                    __('crud.products.inputs.type.label')
                ),

                TextColumn::make('description')
                    ->label(__('crud.products.inputs.description.label'))
                    ->limit(255),

                TextColumn::make('price')->label(
                    __('crud.products.inputs.price.label')
                ),

                TextColumn::make('short_description')
                    ->label(__('crud.products.inputs.short_description.label'))
                    ->limit(255),

                TextColumn::make('stripe_product_id')->label(
                    __('crud.products.inputs.stripe_product_id.label')
                ),

                TextColumn::make('deleted_at')
                    ->label(__('crud.products.inputs.deleted_at.label'))
                    ->since(),

                TextColumn::make('metadata')
                    ->label(__('crud.products.inputs.metadata.label'))
                    ->limit(255),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [RelationManagers\CollectionsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
