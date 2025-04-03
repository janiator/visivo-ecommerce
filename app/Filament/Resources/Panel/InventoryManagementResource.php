<?php
declare(strict_types=1);

namespace App\Filament\Resources\Panel;

use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;
use App\Filament\Resources\Panel\InventoryManagementResource\Pages;

class InventoryManagementResource extends Resource
{
    /**
     * The model the resource represents.
     *
     * @var string|null
     */
    protected static ?string $model = ProductVariant::class;

    /**
     * Navigation configuration.
     */
    protected static ?string $navigationIcon  = 'heroicon-o-table-cells';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?int $navigationSort     = 2;

    /**
     * Scope the query to include only product variants for the current tenant
     * (assumes each variant belongs to a product and each product has a store_id).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $tenant = Filament::getTenant();

        return ProductVariant::query()
            ->whereHas('product', function (Builder $query) use ($tenant): void {
                $query->where('store_id', $tenant->id);
            });
    }

    /**
     * Define the table view for inventory records.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Parent Product name – make sure relationship exists on ProductVariant.
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Variant Name')
                    ->searchable(),

                // Computed column: On-hand inventory (available + committed + unavailable)
                Tables\Columns\TextColumn::make('on_hand')
                    ->label('On Hand')
                    ->formatStateUsing(fn (ProductVariant $record): string => (string) $record->on_hand)
                    ->sortable(),

                Tables\Columns\TextColumn::make('available_stock')
                    ->label('Available')
                    ->sortable(),
                Tables\Columns\TextColumn::make('committed_stock')
                    ->label('Committed')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unavailable_stock')
                    ->label('Unavailable')
                    ->sortable(),
                Tables\Columns\TextColumn::make('incoming_stock')
                    ->label('Incoming')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money(currency: 'NOK')
                    ->sortable(),
            ])
            ->filters([
                // Add any table filters as needed.
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Bulk actions (if required)
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('name');
    }

    /**
     * Define the form used to update inventory records.
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            // Displaying variant information for context.
            Forms\Components\TextInput::make('name')
                ->label('Variant Name')
                ->disabled(),
            Forms\Components\TextInput::make('available_stock')
                ->label('Available Inventory')
                ->numeric()
                ->minValue(0)
                ->required()
                ->hint('Available for sale; not reserved or incoming'),
            Forms\Components\TextInput::make('committed_stock')
                ->label('Committed Inventory')
                ->numeric()
                ->minValue(0)
                ->required()
                ->hint('Part of confirmed orders but not yet fulfilled'),
            Forms\Components\TextInput::make('unavailable_stock')
                ->label('Unavailable Inventory')
                ->numeric()
                ->minValue(0)
                ->required()
                ->hint('Reserved or held for quality control, damage, or apps'),
            Forms\Components\TextInput::make('incoming_stock')
                ->label('Incoming Inventory')
                ->numeric()
                ->minValue(0)
                ->required()
                ->hint('On the way; not yet available for sale'),
            // Show computed on-hand inventory (read-only)
            Forms\Components\TextInput::make('on_hand')
                ->label('On Hand Inventory')
                ->disabled()
                ->helperText('Computed as Available + Committed + Unavailable'),
            // Optionally, show price (for context)
            Forms\Components\TextInput::make('price')
                ->label('Price')
                ->disabled(),
        ]);
    }

    /**
     * Define resource pages/routes.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryManagement::route('/'),
            'edit'  => Pages\EditInventoryManagement::route('/{record}/edit'),
        ];
    }
}
