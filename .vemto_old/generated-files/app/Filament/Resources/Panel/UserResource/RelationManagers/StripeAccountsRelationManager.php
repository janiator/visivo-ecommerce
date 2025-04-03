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

class StripeAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'stripeAccounts';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(['default' => 1])->schema([
                TextInput::make('name')
                    ->label(__('crud.stripeAccounts.inputs.name.label'))
                    ->required()
                    ->string()
                    ->autofocus(),

                TextInput::make('account_id')
                    ->label(__('crud.stripeAccounts.inputs.account_id.label'))
                    ->required()
                    ->string(),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(
                    __('crud.stripeAccounts.inputs.name.label')
                ),

                TextColumn::make('account_id')->label(
                    __('crud.stripeAccounts.inputs.account_id.label')
                ),
            ])
            ->filters([])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
