<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category information')
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
