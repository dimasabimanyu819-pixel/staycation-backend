<?php

namespace App\Filament\Resources\UnitMaintenanceResource\Pages;

use App\Filament\Resources\UnitMaintenanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnitMaintenances extends ListRecords
{
    protected static string $resource = UnitMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
