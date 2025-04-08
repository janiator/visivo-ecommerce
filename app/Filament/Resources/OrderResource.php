<?php declare(strict_types=1);

namespace App\Filament\Resources;

use App\Models\Order;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\KeyValue;
use Filament\Resources\Resource;
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
        return 'bestilling';
    }

    public static function getPluralModelLabel(): string
    {
        return 'bestillinger';
    }

    public static function getNavigationLabel(): string
    {
        return 'Bestillinger';
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                        ])->schema([
                            Forms\Components\Hidden::make('store_id')
                                ->default(fn () => optional(Filament::getTenant())->id),

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

                            // Neat key/value display for metadata
                            KeyValue::make('metadata')
                                ->label(__('crud.orders.inputs.metadata.label'))
                                ->helperText('Legg inn tilleggsdata som nøkkel/verdi-par.'),
                        ]),
                    ]),
                Split::make([
                    Forms\Components\Section::make('Fakturaadresse')
                        ->schema([
                            Forms\Components\TextInput::make('billing_address.line1')
                                ->label('Adresselinje 1')
                                ->nullable(),
                            Forms\Components\TextInput::make('billing_address.line2')
                                ->label('Adresselinje 2')
                                ->nullable(),
                            Split::make([
                                Forms\Components\TextInput::make('billing_address.postal_code')
                                    ->label('Postnummer')
                                    ->nullable(),
                                Forms\Components\TextInput::make('billing_address.city')
                                    ->label('By')
                                    ->nullable(),
                            ]),
                            Forms\Components\TextInput::make('billing_address.country')
                                ->label('Land')
                                ->nullable(),
                        ]),
                    Forms\Components\Section::make('Fraktadresse')
                        ->schema([
                            Forms\Components\TextInput::make('shipping_address.line1')
                                ->label('Adresselinje 1')
                                ->nullable(),
                            Forms\Components\TextInput::make('shipping_address.line2')
                                ->label('Adresselinje 2')
                                ->nullable(),
                            Split::make([
                                Forms\Components\TextInput::make('shipping_address.postal_code')
                                    ->label('Postnummer')
                                    ->nullable(),
                                Forms\Components\TextInput::make('shipping_address.city')
                                    ->label('By')
                                    ->nullable(),
                            ]),
                            Forms\Components\TextInput::make('shipping_address.country')
                                ->label('Land')
                                ->nullable(),
                        ]),
                ]),
            ])
            ->columns(1);
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
                // Column showing count of ordered items
                Tables\Columns\TextColumn::make('order_items_count')
                    ->label('Antall produkter')
                    ->counts('orderItems')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('crud.orders.inputs.total_amount.label'))
                    ->formatStateUsing(function ($state, $record): string {
                        if ($record->currency === 'nok') {
                            return 'kr ' . number_format(((float) $state) / 100, 0) . ',-';
                        }
                        $currencySymbols = [
                            'USD' => '$',
                            'EUR' => '€',
                            'GBP' => '£',
                        ];
                        $symbol = $currencySymbols[$record->currency] ?? $record->currency . ' ';
                        return $symbol . number_format(((float) $state) / 100, 2);
                    }),
                Tables\Columns\TextColumn::make('shipping_address')
                    ->label(__('crud.orders.inputs.shipping_address.label'))
                    ->limit(255),
                Tables\Columns\TextColumn::make('billing_address')
                    ->label(__('crud.orders.inputs.billing_address.label'))
                    ->limit(255),
            ])
            ->filters([
                // Additional filters if needed.
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    /**
     * Override to set "view" as the default page when clicking a table row.
     *
     * @param \App\Models\Order $record
     *
     * @return string|null
     */
    public static function getTableRecordUrl($record): ?string
    {
        return static::getUrl('view', $record);
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
            'view'   => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
