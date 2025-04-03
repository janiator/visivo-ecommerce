<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\StripeProduct;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\Panel\StripeProductResource\Pages;
use App\Filament\Resources\Panel\StripeProductResource\RelationManagers;

class StripeProductResource extends Resource
{
    protected static ?string $model = StripeProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.stripeProducts.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.stripeProducts.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.stripeProducts.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Checkbox::make('active')
                        ->label(__('crud.stripeProducts.inputs.active.label'))
                        ->rules(['boolean'])
                        ->required()
                        ->inline(),

                    Checkbox::make('livemode')
                        ->label(__('crud.stripeProducts.inputs.livemode.label'))
                        ->rules(['boolean'])
                        ->required()
                        ->inline(),

                    DateTimePicker::make('created')
                        ->label(__('crud.stripeProducts.inputs.created.label'))
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    DateTimePicker::make('updated')
                        ->label(__('crud.stripeProducts.inputs.updated.label'))
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    RichEditor::make('description')
                        ->label(
                            __('crud.stripeProducts.inputs.description.label')
                        )
                        ->nullable()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    TextInput::make('images')
                        ->label(__('crud.stripeProducts.inputs.images.label'))
                        ->required()
                        ->string(),

                    RichEditor::make('metadata')
                        ->label(__('crud.stripeProducts.inputs.metadata.label'))
                        ->nullable()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    TextInput::make('name')
                        ->label(__('crud.stripeProducts.inputs.name.label'))
                        ->required()
                        ->string(),

                    RichEditor::make('package_dimensions')
                        ->label(
                            __(
                                'crud.stripeProducts.inputs.package_dimensions.label'
                            )
                        )
                        ->nullable()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    Checkbox::make('shippable')
                        ->label(
                            __('crud.stripeProducts.inputs.shippable.label')
                        )
                        ->rules(['boolean'])
                        ->nullable()
                        ->inline(),

                    TextInput::make('type')
                        ->label(__('crud.stripeProducts.inputs.type.label'))
                        ->required()
                        ->string(),

                    TextInput::make('unit_label')
                        ->label(
                            __('crud.stripeProducts.inputs.unit_label.label')
                        )
                        ->nullable()
                        ->string(),

                    TextInput::make('url')
                        ->label(__('crud.stripeProducts.inputs.url.label'))
                        ->nullable()
                        ->url(),

                    TextInput::make('price')
                        ->label(__('crud.stripeProducts.inputs.price.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    TextInput::make('price_id')
                        ->label(__('crud.stripeProducts.inputs.price_id.label'))
                        ->required()
                        ->string(),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                CheckboxColumn::make('active')->label(
                    __('crud.stripeProducts.inputs.active.label')
                ),

                CheckboxColumn::make('livemode')->label(
                    __('crud.stripeProducts.inputs.livemode.label')
                ),

                TextColumn::make('created')
                    ->label(__('crud.stripeProducts.inputs.created.label'))
                    ->since(),

                TextColumn::make('updated')
                    ->label(__('crud.stripeProducts.inputs.updated.label'))
                    ->since(),

                TextColumn::make('description')
                    ->label(__('crud.stripeProducts.inputs.description.label'))
                    ->limit(255),

                TextColumn::make('images')->label(
                    __('crud.stripeProducts.inputs.images.label')
                ),

                TextColumn::make('metadata')
                    ->label(__('crud.stripeProducts.inputs.metadata.label'))
                    ->limit(255),

                TextColumn::make('name')->label(
                    __('crud.stripeProducts.inputs.name.label')
                ),

                TextColumn::make('package_dimensions')
                    ->label(
                        __(
                            'crud.stripeProducts.inputs.package_dimensions.label'
                        )
                    )
                    ->limit(255),

                CheckboxColumn::make('shippable')->label(
                    __('crud.stripeProducts.inputs.shippable.label')
                ),

                TextColumn::make('type')->label(
                    __('crud.stripeProducts.inputs.type.label')
                ),

                TextColumn::make('unit_label')->label(
                    __('crud.stripeProducts.inputs.unit_label.label')
                ),

                TextColumn::make('url')->label(
                    __('crud.stripeProducts.inputs.url.label')
                ),

                TextColumn::make('price')->label(
                    __('crud.stripeProducts.inputs.price.label')
                ),

                TextColumn::make('price_id')->label(
                    __('crud.stripeProducts.inputs.price_id.label')
                ),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStripeProducts::route('/'),
            'create' => Pages\CreateStripeProduct::route('/create'),
            'view' => Pages\ViewStripeProduct::route('/{record}'),
            'edit' => Pages\EditStripeProduct::route('/{record}/edit'),
        ];
    }
}
