<?php

declare(strict_types=1);

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    /**
     * Get header actions for the page.
     *
     * @return array<int, \Filament\Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Define tabs to filter orders based on the "status" column.
     *
     * @return array<string, \Filament\Resources\Components\Tab>
     */
    public function getTabs(): array
    {
        return [
            'all'      => Tab::make('Alle bestillinger'),
            'complete' => Tab::make('Fullførte')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'complete')),
            'expired'  => Tab::make('Utløpte')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'expired')),
            'open'     => Tab::make('Åpne')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'open')),
        ];
    }

    /**
     * Set the default active tab when the page loads.
     *
     * @return string|int|null
     */
    public function getDefaultActiveTab(): string|int|null
    {
        return 'complete';
    }
}
