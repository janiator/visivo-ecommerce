<?php

namespace App\Filament\Resources\Panel\StripeProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\StripeProductResource;

class ViewStripeProduct extends ViewRecord
{
    protected static string $resource = StripeProductResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
