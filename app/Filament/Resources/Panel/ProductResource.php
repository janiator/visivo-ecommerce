<?php

declare(strict_types=1);

namespace App\Filament\Resources\Panel;

use App\Filament\Resources\Panel\ProductResource\RelationManagers\CollectionsRelationManager;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\Group;
use Filament\Tables;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Panel\ProductResource\Pages;
use Filament\Facades\Filament;

class ProductResource extends Resource
{
    /**
     * The model the resource represents.
     *
     * @var string|null
     */
    protected static ?string $model = Product::class;


    /**
     * Multi-tenancy: specify the relationship on the Product model that connects it to a store.
     * Since each product belongs to a single store via store_id, we set it to 'store'.
     *
     * @var string|null
     */
    protected static ?string $tenantOwnershipRelationshipName = 'store';

    // Navigation configuration.
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Butikk';

    /**
     * Labels used in the Filament UI.
     */
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


    /**
     * Define the table used to list products.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s') // Auto-refresh the table every 60 seconds.
            ->columns([
                // Computed column displays the variant name.
                // When using the "all" filter, it will use the joined field, otherwise fallback to the main variant.
                Tables\Columns\TextColumn::make('name')
                    ->label(__('crud.products.inputs.name.label'))
                    ->getStateUsing(fn ($record): ?string => $record->variant_name ?? $record->mainVariant?->name),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('crud.products.inputs.status.label')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('crud.products.inputs.name.label')),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('crud.products.inputs.type.label')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('crud.products.inputs.description.label'))
                    ->limit(255),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('crud.products.inputs.price.label')),
                Tables\Columns\TextColumn::make('short_description')
                    ->label(__('crud.products.inputs.short_description.label'))
                    ->limit(255),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('variant_type')
                    ->default('main') // Set the default filter to show only main variants.
                    ->label('Variant Type')
                    ->options([
                        'main' => 'Only Main Variant',
                        'all'  => 'All Variants',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // When the user selects "all" display all product variant rows.
                        // Otherwise, do not alter the query so that only the main variant is used in the computed column.
                        if ((($data['value'] ?? 'main')) === 'all') {
                            return $query->join(
                                'product_variants',
                                'product_variants.product_id',
                                '=',
                                'products.id'
                            )->addSelect('product_variants.name as variant_name');
                        }

                        return $query;
                    }),
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

    /**
     * Define the form used for creating and editing products.
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            // Attach the current store using a hidden field.
            Forms\Components\Hidden::make('store_id')
                ->default(fn () => optional(Filament::getTenant())->id),
            // Basic Details Section.
            Split::make([
                Section::make(__('crud.products.section.details'))
                    ->schema([
                        // Grid wrapping general product info.
                        Forms\Components\Grid::make(['default' => 1])
                            ->schema([

                                Forms\Components\TextInput::make('name')
                                    ->label(__('crud.products.inputs.name.label'))
                                    ->required()
                                    ->string(),
                                Forms\Components\Select::make('type')
                                    ->label(__('crud.products.inputs.type.label'))
                                    ->options([
                                        'physical'     => 'Physical',
                                        'digital'      => 'Digital',
                                        'service'      => 'Service',
                                        'subscription' => 'Subscription',
                                        'event'        => 'Event',
                                    ])
                                    ->nullable(),
                                //TODO Consider removing short description completely
//                                Forms\Components\RichEditor::make('short_description')
//                                    ->label(__('crud.products.inputs.short_description.label'))
//                                    ->nullable()
//                                    ->string()
//                                    ->fileAttachmentsVisibility('public'),
                                Forms\Components\RichEditor::make('description')
                                    ->label(__('crud.products.inputs.description.label'))
                                    ->nullable()
                                    ->string()
                                    ->fileAttachmentsVisibility('public'),
                                Forms\Components\TextInput::make('price')
                                    ->label(__('crud.products.inputs.price.label'))
                                    ->nullable()
                                    ->numeric()
                                    ->step(1),
                            ]),

                    ])->columns(1),


                Group::make([
                    Section::make(__('crud.products.inputs.status.label'))
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->label(__('crud.products.inputs.status.label'))
                                ->required()
                                ->options([
                                    'published' => 'Published',
                                    'draft'     => 'Draft',
                                    'archived'  => 'Archived',
                                ]),
                    ]),
                    // Images Section.
                    Section::make(__('Bilder'))
                        ->schema([
                            Forms\Components\SpatieMediaLibraryFileUpload::make('main_image')
                                ->collection('main_images')
                                ->disk('s3')
                                ->label(__('crud.products.inputs.main_image.label')),
                            Forms\Components\SpatieMediaLibraryFileUpload::make('gallery_images')
                                ->collection('gallery_images')
                                ->disk('s3')
                                ->multiple()
                                ->maxFiles(10)
                                ->nullable()
                                ->label(__('crud.products.inputs.gallery_images.label')),
                        ])->columns(1),
                    // Collections Relationship Section.
                    Forms\Components\Section::make(__('Collections'))
                        ->description(__('Select the collections that this product belongs to.'))
                        ->schema([
                            Forms\Components\Select::make('collections')
                                ->label(__('Samlinger'))
                                ->multiple()
                                ->relationship('collections', 'name')
                                ->preload(),
                        ]),
                ])->grow(false),
            ])->from('md'),



        ])->columns(1);
    }

    /**
     * Define any resource relations.
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Define the resource pages/routes.
     */
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view'   => Pages\ViewProduct::route('/{record}'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
