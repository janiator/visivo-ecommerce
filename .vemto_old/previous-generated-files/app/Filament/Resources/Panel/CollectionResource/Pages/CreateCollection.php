<?php

namespace App\Filament\Resources\Panel\CollectionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Panel\CollectionResource;

class CreateCollection extends CreateRecord
{
    protected static string $resource = CollectionResource::class;
}
