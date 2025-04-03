<?php

namespace App\Filament\Resources\Panel\ImageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\ImageResource;

class ListImages extends ListRecords
{
    protected static string $resource = ImageResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
