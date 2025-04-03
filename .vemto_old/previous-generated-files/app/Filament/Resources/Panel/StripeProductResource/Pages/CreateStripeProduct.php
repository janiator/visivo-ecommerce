<?php

namespace App\Filament\Resources\Panel\StripeProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Panel\StripeProductResource;

class CreateStripeProduct extends CreateRecord
{
    protected static string $resource = StripeProductResource::class;
}
