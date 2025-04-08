<?php

declare(strict_types=1);

namespace App\Filament\Resources\Panel\ProductResource\Pages;

use App\Models\ProductMetaKey;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\ProductResource;
use Filament\Support\Enums\MaxWidth;
use App\Jobs\SyncStripeProductJob;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    /**
     * Customize the maximum width of the content.
     */
    // public function getMaxContentWidth(): MaxWidth
    // {
    //     return MaxWidth::Full;
    // }

    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()->formId('form'),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * After the product is saved, update its meta values,
     * force a touch to trigger observers, and dispatch the
     * job to sync the product with Stripe.
     *
     * @return void
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

        // Force an update of the updated_at timestamp.
        $record->touch();

        // Dispatch the job to sync the product with Stripe.
        //SyncStripeProductJob::dispatch($record->store_id, $record->id, 'updated');
    }
}
