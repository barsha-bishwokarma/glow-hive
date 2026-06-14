<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Brand information')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        Textarea::make('description')
                            ->default(null),
                    ])->columns(2)
            ]);
    }
}
