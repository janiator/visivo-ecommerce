<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\StripeAccount;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Panel\StripeAccountResource\Pages;
use App\Filament\Resources\Panel\StripeAccountResource\RelationManagers;

class StripeAccountResource extends Resource
{
    protected static ?string $model = StripeAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.stripeAccounts.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.stripeAccounts.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.stripeAccounts.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('name')
                        ->label(__('crud.stripeAccounts.inputs.name.label'))
                        ->required()
                        ->string()
                        ->autofocus(),

                    TextInput::make('account_id')
                        ->label(
                            __('crud.stripeAccounts.inputs.account_id.label')
                        )
                        ->required()
                        ->string(),

                    Select::make('user_id')
                        ->label('User')
                        ->required()
                        ->relationship('user', 'name')
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
                TextColumn::make('name')->label(
                    __('crud.stripeAccounts.inputs.name.label')
                ),

                TextColumn::make('account_id')->label(
                    __('crud.stripeAccounts.inputs.account_id.label')
                ),

                TextColumn::make('user.name')->label('User'),
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
            'index' => Pages\ListStripeAccounts::route('/'),
            'create' => Pages\CreateStripeAccount::route('/create'),
            'view' => Pages\ViewStripeAccount::route('/{record}'),
            'edit' => Pages\EditStripeAccount::route('/{record}/edit'),
        ];
    }
}
