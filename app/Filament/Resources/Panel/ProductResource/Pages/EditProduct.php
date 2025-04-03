<?php

namespace App\Filament\Resources\Panel\ProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\ProductResource;
use Filament\Support\Enums\MaxWidth;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    /**
     * Customize the maximum width of the content.
     */
//    public function getMaxContentWidth(): MaxWidth
//    {
//        return MaxWidth::Full;
//    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()->formId('form'),
            Actions\DeleteAction::make(),
        ];
    }
}
