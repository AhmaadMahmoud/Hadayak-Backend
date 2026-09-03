<?php

namespace App\Filament\Resources\CardDesigns\Pages;

use App\Filament\Resources\CardDesigns\CardDesignResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCardDesigns extends ManageRecords
{
    protected static string $resource = CardDesignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('إضافة كارت'),
        ];
    }
}
