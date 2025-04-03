<?php

namespace App\Filament\Resources\Panel\StripeAccountResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\StripeAccountResource;

class ListStripeAccounts extends ListRecords
{
    protected static string $resource = StripeAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
