<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->required()
                    ->relationship('product', 'name'),
                Select::make('user_id')
                    ->required()
                    ->relationship('user', 'name'),
                TextInput::make('rating')
                    ->required()
                    ->numeric(),
                TextInput::make('title')
                    ->default(null),
                Textarea::make('body')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('is_approved')
                    ->required(),
            ]);
    }
}
