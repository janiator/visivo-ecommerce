<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Collection;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\CheckboxColumn;
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
                Grid::make(['default' => 1])->schema([
                    Select::make('store_id')
                        ->label('Store')
                        ->required()
                        ->relationship('store', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('name')
                        ->label(__('crud.collections.inputs.name.label'))
                        ->required()
                        ->string(),

                    Checkbox::make('visible')
                        ->label(__('crud.collections.inputs.visible.label'))
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
                TextColumn::make('store.name')->label('Store'),

                TextColumn::make('name')->label(
                    __('crud.collections.inputs.name.label')
                ),

                CheckboxColumn::make('visible')->label(
                    __('crud.collections.inputs.visible.label')
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
            'index' => Pages\ListCollections::route('/'),
            'create' => Pages\CreateCollection::route('/create'),
            'view' => Pages\ViewCollection::route('/{record}'),
            'edit' => Pages\EditCollection::route('/{record}/edit'),
        ];
    }
}
