<?php

namespace App\Filament\Resources\Panel;

use App\Models\Collection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\CheckboxColumn;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Filament\Facades\Filament;
use App\Filament\Resources\Panel\CollectionResource\Pages;
use App\Filament\Resources\Panel\CollectionResource\RelationManagers;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;

    /**
     * Define the tenant relationship name for multi-tenancy.
     *
     * @var string
     */
    protected static ?string $tenantOwnershipRelationshipName = 'store';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Butikk';

    public static function getModelLabel(): string
    {
        return 'kolleksjon';
    }

    public static function getPluralModelLabel(): string
    {
        return 'kolleksjoner';
    }

    public static function getNavigationLabel(): string
    {
        return 'Kolleksjoner';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make([
                    'default' => 1,
                ])->schema([
                    Hidden::make('store_id')
                        ->default(fn () => optional(Filament::getTenant())->id),
                    TextInput::make('name')
                        ->label('Navn')
                        ->required()
                        ->string(),
                    Checkbox::make('visible')
                        ->label('Synlig')
                        ->rules(['boolean'])
                        ->required()
                        ->inline(),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('name')
                    ->label('Navn')
                    ->searchable()
                    ->sortable(),
                // New column: count the number of products in the collection.
                TextColumn::make('products_count')
                    ->label('Antall produkter')
                    ->counts('products')
                    ->sortable(),
                CheckboxColumn::make('visible')
                    ->label('Synlig')
                    ->sortable(),
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
            RelationManagers\ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCollections::route('/'),
            'create' => Pages\CreateCollection::route('/create'),
            'view'   => Pages\ViewCollection::route('/{record}'),
            'edit'   => Pages\EditCollection::route('/{record}/edit'),
        ];
    }
}
