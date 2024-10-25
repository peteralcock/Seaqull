<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
