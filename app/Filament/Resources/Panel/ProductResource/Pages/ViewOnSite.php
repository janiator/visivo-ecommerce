<?php

declare(strict_types=1);

namespace App\Filament\Resources\Panel\ProductResource\Pages;

use App\Filament\Resources\Panel\ProductResource;
use Filament\Resources\Pages\ViewRecord;

/**
 * Class ViewOnSite
 *
 * A custom page that renders the product as it would appear on the e-commerce site.
 *
 * @package App\Filament\Resources\Panel\ProductResource\Pages
 */
class ViewOnSite extends ViewRecord
{
    /**
     * The resource the page is associated with.
     *
     * @var string
     */
    protected static string $resource = ProductResource::class;

    /**
     * The URL slug for the page.
     *
     * @var string|null
     */
    protected static ?string $slug = 'view-on-site';

    /**
     * Override the default view with a custom view.
     *
     * Ensure you have created the corresponding Blade file in:
     * resources/views/filament/resources/panel/product-resource/pages/view-on-site.blade.php
     *
     * @var string
     */
    protected static string $view = 'filament.resources.panel.product-resource.pages.view-on-site';

    /**
     * Get the page title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return __('Preview: ') . $this->record->name;
    }
}
