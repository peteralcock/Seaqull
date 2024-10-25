<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $slug = 'reports';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('prompt')
                    ->required(),

                TextInput::make('query')
                    ->required(),

                Select::make('database.host')
                    ->relationship('database', 'host')
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Report $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Report $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('prompt'),

                TextColumn::make('query')->badge()->copyable(),

                TextColumn::make('database.host'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
