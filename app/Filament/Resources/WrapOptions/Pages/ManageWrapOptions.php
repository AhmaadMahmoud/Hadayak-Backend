<?php

namespace App\Filament\Resources\WrapOptions\Pages;

use App\Filament\Resources\WrapOptions\WrapOptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWrapOptions extends ManageRecords
{
    protected static string $resource = WrapOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('إضافة تغليف'),
        ];
    }
}
