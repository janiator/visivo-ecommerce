<?php

namespace App\Filament\Resources\Panel\StripeProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\StripeProductResource;

class EditStripeProduct extends EditRecord
{
    protected static string $resource = StripeProductResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
