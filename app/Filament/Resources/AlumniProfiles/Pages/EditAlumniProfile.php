<?php

namespace App\Filament\Resources\AlumniProfiles\Pages;

use App\Filament\Resources\AlumniProfiles\AlumniProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAlumniProfile extends EditRecord
{
    protected static string $resource = AlumniProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
