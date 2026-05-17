<?php

namespace App\Filament\Resources\AlumniProfiles\Pages;

use App\Filament\Resources\AlumniProfiles\AlumniProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAlumniProfiles extends ListRecords
{
    protected static string $resource = AlumniProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
