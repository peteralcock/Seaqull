<?php

namespace App\Filament\Resources\DatabaseResource\Pages;

use App\Filament\Resources\DatabaseResource;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDatabases extends ListRecords
{
    protected static string $resource = DatabaseResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
