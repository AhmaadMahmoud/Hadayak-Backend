<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('create');
    }

    protected function afterCreate(): void
    {
        ProductResource::syncImages(
            $this->record,
            $this->form->getRawState()['images_upload'] ?? null,
        );
    }
}
