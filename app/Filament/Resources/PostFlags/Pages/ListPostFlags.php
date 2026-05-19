<?php

namespace App\Filament\Resources\PostFlags\Pages;

use App\Filament\Resources\PostFlags\PostFlagResource;
use Filament\Resources\Pages\ListRecords;

class ListPostFlags extends ListRecords
{
    protected static string $resource = PostFlagResource::class;
}
