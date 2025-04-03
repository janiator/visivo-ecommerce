<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use App\Models\Image;
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
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\Panel\ImageResource\Pages;
use App\Filament\Resources\Panel\ImageResource\RelationManagers;

class ImageResource extends Resource
{
    protected static ?string $model = Image::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.images.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.images.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.images.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    FileUpload::make('filename')
                        ->label(__('crud.images.inputs.filename.label'))
                        ->rules(['file'])
                        ->required(
                            fn(string $context): bool => $context === 'create'
                        )
                        ->dehydrated(fn($state) => filled($state))
                        ->maxSize(1024),

                    TextInput::make('mime_type')
                        ->label(__('crud.images.inputs.mime_type.label'))
                        ->nullable()
                        ->string(),

                    TextInput::make('public_url')
                        ->label(__('crud.images.inputs.public_url.label'))
                        ->nullable()
                        ->string(),

                    Select::make('store_id')
                        ->label('Store')
                        ->required()
                        ->relationship('store', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('filename')->label(
                    __('crud.images.inputs.filename.label')
                ),

                TextColumn::make('mime_type')->label(
                    __('crud.images.inputs.mime_type.label')
                ),

                TextColumn::make('public_url')->label(
                    __('crud.images.inputs.public_url.label')
                ),

                TextColumn::make('store.name')->label('Store'),
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
            'index' => Pages\ListImages::route('/'),
            'create' => Pages\CreateImage::route('/create'),
            'view' => Pages\ViewImage::route('/{record}'),
            'edit' => Pages\EditImage::route('/{record}/edit'),
        ];
    }
}
