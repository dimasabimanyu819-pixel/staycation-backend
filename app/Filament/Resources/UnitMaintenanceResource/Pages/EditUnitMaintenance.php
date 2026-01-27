<?php

namespace App\Filament\Resources\UnitMaintenanceResource\Pages;

use App\Filament\Resources\UnitMaintenanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitMaintenance extends EditRecord
{
    protected static string $resource = UnitMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
