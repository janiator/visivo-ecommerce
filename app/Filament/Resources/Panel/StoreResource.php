<?php

declare(strict_types=1);

namespace App\Filament\Resources\Panel;

use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use App\Filament\Resources\Panel\StoreResource\Pages;
use App\Filament\Resources\Panel\StoreResource\RelationManagers;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    // Do not set tenantOwnershipRelationshipName here.
    // That would force Filament to apply tenant scoping automatically.
    // We are handling it conditionally via getEloquentQuery() instead.
    protected static ?string $tenantOwnershipRelationshipName = 'store';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.stores.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.stores.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.stores.collectionTitle');
    }

    /**
     * Override the default query.
     *
     * For user id 1 (the super‑admin), return all stores.
     * For others, limit the query to only those stores that are linked
     * via the pivot table (store_user) to the authenticated user.
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        // Start with a base Eloquent query from the model
        $query = Store::query();

        if (auth()->check() && auth()->id() !== 1) {
            // For non‑admin users, add a filtering condition on the pivot.
            $query->whereHas('users', function (Builder $query): void {
                $query->where('users.id', auth()->id());
            });
        }

        return $query;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('name')
                        ->label(__('crud.stores.inputs.name.label'))
                        ->required()
                        ->string()
                        ->autofocus(),
                    TextInput::make('stripe_account_id')
                        ->label(__('crud.stores.inputs.stripe_account_id.label'))
                        ->nullable()
                        ->string(),
                ]),
            ]),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('name')
                    ->label(__('crud.stores.inputs.name.label')),
                TextColumn::make('stripe_account_id')
                    ->label(__('crud.stores.inputs.stripe_account_id.label')),
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
        return [
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'view'   => Pages\ViewStore::route('/{record}'),
            'edit'   => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}
