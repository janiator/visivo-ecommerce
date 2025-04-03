<?php
declare(strict_types=1);

namespace App\Filament\Resources\Panel\InventoryManagementResource\Pages;

use App\Filament\Resources\Panel\InventoryManagementResource;
use Filament\Resources\Pages\EditRecord;

class EditInventoryManagement extends EditRecord
{
    protected static string $resource = InventoryManagementResource::class;
}
