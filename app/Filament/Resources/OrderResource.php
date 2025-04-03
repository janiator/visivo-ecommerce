<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Models\Order;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Resources\Components\Tab;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $tenantOwnershipRelationshipName = 'store';

    protected static ?string $navigationGroup = 'Butikk';

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

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Grid::make(['default' => 1])->schema([
                    // Store SELECT – hide store field later for multi-tenancy
                    Forms\Components\Select::make('store_id')
                        ->label('Store')
                        ->required()
                        ->relationship('store', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),
                    // Customer SELECT
                    Forms\Components\Select::make('customer_id')
                        ->label('Customer')
                        ->nullable()
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),
                    Forms\Components\TextInput::make('stripe_order_id')
                        ->label(__('crud.orders.inputs.stripe_order_id.label'))
                        ->required()
                        ->string()
                        ->unique('orders', 'stripe_order_id', ignoreRecord: true),
                    Forms\Components\TextInput::make('total_amount')
                        ->label(__('crud.orders.inputs.total_amount.label'))
                        ->required()
                        ->numeric()
                        ->step(1),
                    Forms\Components\TextInput::make('currency')
                        ->label(__('crud.orders.inputs.currency.label'))
                        ->required()
                        ->string(),
                    Forms\Components\RichEditor::make('shipping_address')
                        ->label(__('crud.orders.inputs.shipping_address.label'))
                        ->nullable()
                        ->fileAttachmentsVisibility('public'),
                    Forms\Components\RichEditor::make('billing_address')
                        ->label(__('crud.orders.inputs.billing_address.label'))
                        ->nullable()
                        ->fileAttachmentsVisibility('public'),
                    Forms\Components\RichEditor::make('metadata')
                        ->label(__('crud.orders.inputs.metadata.label'))
                        ->nullable()
                        ->fileAttachmentsVisibility('public'),
                ]),
            ]),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->poll('60s')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Store')
                    ->hidden(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('crud.orders.inputs.total_amount.label'))
                    ->formatStateUsing(function ($state, $record): string {
                        // If the order is in NOK, show as "kr {amount},-"
                        if ($record->currency === 'nok') {
                            return 'kr ' . number_format(((float)$state) / 100, 0) . ',-';
                        }
                        // Currency symbol map for other currencies (extend as needed)
                        $currencySymbols = [
                            'USD' => '$',
                            'EUR' => '€',
                            'GBP' => '£',
                        ];
                        $symbol = $currencySymbols[$record->currency] ?? $record->currency . ' ';
                        return $symbol . number_format(((float)$state) / 100, 2);
                    }),
                Tables\Columns\TextColumn::make('shipping_address')
                    ->label(__('crud.orders.inputs.shipping_address.label'))
                    ->limit(255),
                Tables\Columns\TextColumn::make('billing_address')
                    ->label(__('crud.orders.inputs.billing_address.label'))
                    ->limit(255),
            ])
            ->filters([
                // You could add additional filters here if necessary.
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
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
