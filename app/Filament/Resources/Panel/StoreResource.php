<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use App\Models\Store;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Panel\StoreResource\Pages;
use App\Filament\Resources\Panel\StoreResource\RelationManagers;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    /**
     * For tenant scoping, use the 'users' relationship.
     *
     * This tells Filament to check the users associated with the store,
     * filtering the instances based on the currently authenticated user.
     *
     * @var string|null
     */
    protected static ?string $tenantOwnershipRelationshipName = 'users';

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

    public static function form(Form $form): Form
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
                        ->label(
                            __('crud.stores.inputs.stripe_account_id.label')
                        )
                        ->nullable()
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
                TextColumn::make('name')->label(
                    __('crud.stores.inputs.name.label')
                ),

                TextColumn::make('stripe_account_id')->label(
                    __('crud.stores.inputs.stripe_account_id.label')
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
        return [RelationManagers\UsersRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'view' => Pages\ViewStore::route('/{record}'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}
