<?php

namespace App\Filament\Resources\Panel\CollectionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\CollectionResource;

class ListCollections extends ListRecords
{
    protected static string $resource = CollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
