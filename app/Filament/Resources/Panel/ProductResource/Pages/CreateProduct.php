<?php declare(strict_types=1);

namespace App\Filament\Resources\Panel\ProductResource\Pages;

use App\Filament\Resources\Panel\ProductResource;
use App\Models\ProductMetaKey;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    /**
     * After the product is saved, update its meta values.
     */
    protected function afterSave(): void
    {
        $metaPairs = $this->form->getState()['meta_values'] ?? [];
        $record = $this->record;

        // Remove any existing meta values.
        $record->metaValues()->delete();

        // Loop through each key/value pair and update via the relation.
        foreach ($metaPairs as $key => $value) {
            // Find or create the meta key record for the store.
            $metaKey = ProductMetaKey::firstOrCreate(
                [
                    'store_id' => $record->store_id,
                    'key'      => $key,
                ],
                [
                    'data_type' => 'string',
                ]
            );
            $record->metaValues()->create([
                'meta_key_id' => $metaKey->id,
                'value'       => $value,
            ]);
        }
    }
}
