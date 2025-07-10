<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockWidget extends BaseWidget
{
    protected static ?string $heading = 'Low Stock Alert - Products Below 10 Units';
    
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('stock', '<', 10)
                    ->orderBy('stock', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brand')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Current Stock')
                    ->sortable()
                    ->numeric()
                    ->color(fn (Product $record): string => match (true) 
                    {
                        $record->stock == 0 => 'danger',
                        $record->stock <= 3 => 'warning',
                        default => 'primary'
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Unit Price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn (Product $record): string => match (true) 
                    {
                        $record->stock == 0 => 'Out of Stock',
                        $record->stock <= 3 => 'Critical',
                        default => 'Low Stock'
                    })
                    ->badge()
                    ->color(fn (Product $record): string => match (true) 
                    {
                        $record->stock == 0 => 'danger',
                        $record->stock <= 3 => 'warning',
                        default => 'primary'
                    }),
            ])
            ->defaultSort('stock', 'asc')
            ->emptyStateHeading('Great! No products are low on stock')
            ->emptyStateDescription('All products have sufficient inventory levels.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}