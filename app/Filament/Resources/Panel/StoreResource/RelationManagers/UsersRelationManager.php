<?php

namespace App\Filament\Resources\Panel\StoreResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Panel\StoreResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions;
use Filament\Tables\Actions\AttachAction;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(['default' => 1])->schema([
                TextInput::make('name')
                    ->label(__('crud.users.inputs.name.label'))
                    ->required()
                    ->string()
                    ->autofocus(),

                TextInput::make('email')
                    ->label(__('crud.users.inputs.email.label'))
                    ->required()
                    ->string()
                    ->unique('users', 'email', ignoreRecord: true)
                    ->email(),

                TextInput::make('password')
                    ->label(__('crud.users.inputs.password.label'))
                    ->required(
                        fn(string $context): bool => $context === 'create'
                    )
                    ->dehydrated(fn($state) => filled($state))
                    ->string()
                    ->minLength(6)
                    ->password(),

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
                    __('crud.users.inputs.name.label')
                ),

                TextColumn::make('email')->label(
                    __('crud.users.inputs.email.label')
                ),

                TextColumn::make('role')->label(
                    __('crud.storeUser.inputs.role.label')
                ),
            ])
            ->filters([])
            ->headerActions([
                Actions\CreateAction::make(),
                AttachAction::make()->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('role')->required(),
                    ]),
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
