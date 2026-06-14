<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->sortable(),  
                TextColumn::make('brand.name')
                    ->sortable(),  
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('price')
                    ->prefix('Rs.')
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->prefix('Rs.')
                    ->sortable(),
                TextColumn::make('stock_quantity')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_active'),  
                ToggleColumn::make('is_featured'), 
                TextColumn::make('average_rating')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('review_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
