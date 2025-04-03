<?php

namespace App\Filament\Resources\Panel\StripeAccountResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\StripeAccountResource;

class ViewStripeAccount extends ViewRecord
{
    protected static string $resource = StripeAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
