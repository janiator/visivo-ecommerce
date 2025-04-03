<?php

namespace App\Filament\Resources\Panel\StripeAccountResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\StripeAccountResource;

class EditStripeAccount extends EditRecord
{
    protected static string $resource = StripeAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
