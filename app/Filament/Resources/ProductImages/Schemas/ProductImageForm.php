<?php

namespace App\Filament\Resources\ProductImages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Upload ProductImages')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required(),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        FileUpload::make('image')
                            ->image()
                            ->columnSpanFull()
                            ->required(),
                    ])->columns(2)

            ]);
    }
}
