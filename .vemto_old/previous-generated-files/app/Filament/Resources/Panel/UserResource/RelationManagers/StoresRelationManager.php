<?php

namespace App\Filament\Resources\Panel\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Panel\UserResource;
use Filament\Resources\RelationManagers\RelationManager;

class StoresRelationManager extends RelationManager
{
    protected static string $relationship = 'stores';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form->schema([
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

                TextInput::make('role')
                    ->label(__('crud.storeUser.inputs.role.label'))
                    ->required()
                    ->string()
                    ->autofocus(),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(
                    __('crud.stores.inputs.name.label')
                ),

                TextColumn::make('stripe_account_id')->label(
                    __('crud.stores.inputs.stripe_account_id.label')
                ),

                TextColumn::make('role')->label(
                    __('crud.storeUser.inputs.role.label')
                ),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),

                Tables\Actions\AttachAction::make()->form(
                    fn(Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),

                        TextInput::make('role')
                            ->label(__('crud.storeUser.inputs.role.label'))
                            ->required()
                            ->string()
                            ->autofocus(),
                    ]
                ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
