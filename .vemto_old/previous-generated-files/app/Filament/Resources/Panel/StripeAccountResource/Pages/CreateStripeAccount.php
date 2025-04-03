<?php

namespace App\Filament\Resources\Panel\StripeAccountResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Panel\StripeAccountResource;

class CreateStripeAccount extends CreateRecord
{
    protected static string $resource = StripeAccountResource::class;
}
