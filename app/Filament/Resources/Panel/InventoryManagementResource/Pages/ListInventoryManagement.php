<?php
declare(strict_types=1);

namespace App\Filament\Resources\Panel\InventoryManagementResource\Pages;

use App\Filament\Resources\Panel\InventoryManagementResource;
use Filament\Resources\Pages\ListRecords;

class ListInventoryManagement extends ListRecords
{
    protected static string $resource = InventoryManagementResource::class;
}
