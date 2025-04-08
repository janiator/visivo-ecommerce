<?php declare(strict_types=1);

namespace App\Filament\Resources\Panel;

use App\Filament\Resources\Panel\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
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
                // Main image thumbnail column.
                ImageColumn::make('main_image')
                    ->label('')
                    ->getStateUsing(function (Product $record): ?string {
                        // Assuming the image is stored in the 'main_image' collection;
                        // adjust the collection name as needed.
                        return $record->getFirstMediaUrl('main_image');
                    })
                    ->circular() // Optional: displays the image as a circular thumbnail.
                    ->height(50)
                    ->width(50),
                // Status as colored tags.
                BadgeColumn::make('status')
                    ->label(__('crud.products.inputs.status.label'))
                    ->colors([
                        'success' => 'active',
                        'warning' => 'draft',
                        'gray'  => 'archived',
                    ]),

                TextColumn::make('name')
                    ->label(__('crud.products.inputs.name.label')),

                TextColumn::make('type')
                    ->label(__('crud.products.inputs.type.label')),

                TextColumn::make('description')
                    ->label(__('crud.products.inputs.description.label'))
                    ->formatStateUsing(fn (string $state): string => strip_tags($state))
                    ->limit(100),

                TextColumn::make('price')
                    ->formatStateUsing(function ($state, $record): string {
                        // If the order is in NOK, show as "kr {amount},-" else use currency symbols.
                        if ($record->currency === 'nok' || $record->currency === null) {
                            return 'kr ' . number_format(((float)$state) / 100, 0) . ',-';
                        }
                        $currencySymbols = [
                            'USD' => '$',
                            'EUR' => '€',
                            'GBP' => '£',
                        ];
                        $symbol = $currencySymbols[$record->currency] ?? $record->currency . ' ';
                        return $symbol . number_format(((float)$state) / 100, 2);
                    }),
            ])
            ->filters([
                // Add any filters you need.
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
                                        'physical' => 'Physical',
                                        'digital' => 'Digital',
                                        'service' => 'Service',
                                        'subscription' => 'Subscription',
                                        'event' => 'Event',
                                    ])
                                    ->nullable(),

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
                    ])
                    ->columns(1),

                Group::make([
                    Section::make(__('crud.products.inputs.status.label'))
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->label(__('crud.products.inputs.status.label'))
                                ->required()
                                ->options([
                                    'active' => 'Active',
                                    'draft' => 'Draft',
                                    'archived' => 'Archived',
                                ]),
                        ]),

                    // Images Section.
                    Section::make(__('Bilder'))
                        ->schema([
                            Forms\Components\SpatieMediaLibraryFileUpload::make('main_image')
                                ->collection('main_image')
                                ->disk('s3')
                                ->label(__('crud.products.inputs.main_image.label')),

                            Forms\Components\SpatieMediaLibraryFileUpload::make('gallery_images')
                                ->collection('gallery_images')
                                ->disk('s3')
                                ->multiple()
                                ->maxFiles(10)
                                ->nullable()
                                ->label(__('crud.products.inputs.gallery_images.label')),
                        ])
                        ->columns(1),

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
                ])
                    ->columns(1),
            ])
                ->grow(false)
                ->from('md'),
        ])
            ->columns(1);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
