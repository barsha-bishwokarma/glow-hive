<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Information')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required(),
                        Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->required(),
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('stock_quantity')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Textarea::make('description')
                            ->default(null)
                            ->columnSpanFull(),
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('Rs.'),
                        TextInput::make('sale_price')
                            ->numeric()
                            ->default(null)
                            ->prefix('Rs.'),
                    ])->columns(2),
            ]);
    }
}
