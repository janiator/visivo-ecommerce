<?php

namespace App\Filament\Resources\Panel\StripeProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\StripeProductResource;

class ListStripeProducts extends ListRecords
{
    protected static string $resource = StripeProductResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
