<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Livewire\Component;
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
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;


class OrderResource extends Resource
{



    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $tenantOwnershipRelationshipName = 'store';


    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return 'Bestilling';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Bestillinger';
    }

    public static function getNavigationLabel(): string
    {
        return 'Bestillinger';
    }

//TODO fix translations
    public static function form(Form $form): Form
    {

        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    //TODO hide store and make sure only orders from current store are shown
                    //TODO only show success orders by default
                    Select::make('store_id')
                        ->label('Store')
                        ->required()
                        ->relationship('store', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('customer_id')
                        ->label('Customer')
                        ->nullable()
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('stripe_order_id')
                        ->label(__('crud.orders.inputs.stripe_order_id.label'))
                        ->required()
                        ->string()
                        ->unique(
                            'orders',
                            'stripe_order_id',
                            ignoreRecord: true
                        ),

                    TextInput::make('total_amount')
                        ->label(__('crud.orders.inputs.total_amount.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    TextInput::make('currency')
                        ->label(__('crud.orders.inputs.currency.label'))
                        ->required()
                        ->string(),

                    RichEditor::make('shipping_address')
                        ->label(__('crud.orders.inputs.shipping_address.label'))
                        ->nullable()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    RichEditor::make('billing_address')
                        ->label(__('crud.orders.inputs.billing_address.label'))
                        ->nullable()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    RichEditor::make('metadata')
                        ->label(__('crud.orders.inputs.metadata.label'))
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

                TextColumn::make('customer.name')->label('Customer'),

                TextColumn::make('stripe_order_id')->label(
                    __('crud.orders.inputs.stripe_order_id.label')
                ),

                TextColumn::make('total_amount')->label(
                    __('crud.orders.inputs.total_amount.label')
                ),

                TextColumn::make('currency')->label(
                    __('crud.orders.inputs.currency.label')
                ),

                TextColumn::make('shipping_address')
                    ->label(__('crud.orders.inputs.shipping_address.label'))
                    ->limit(255),

                TextColumn::make('billing_address')
                    ->label(__('crud.orders.inputs.billing_address.label'))
                    ->limit(255),

                TextColumn::make('metadata')
                    ->label(__('crud.orders.inputs.metadata.label'))
                    ->limit(255),


            ])
            ->filters([

            ])
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

        return [
            RelationManagers\OrderItemsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {

        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),

        ];
    }
}
