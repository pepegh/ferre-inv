<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\SaleItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'Most Sold Products (Last 30 Days)';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->select('products.*')
                    ->selectRaw('SUM(sale_items.quantity) as total_sold')
                    ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.date', '>=', now()->subDays(30))
                    ->groupBy('products.id')
                    ->orderByDesc('total_sold')
                    ->limit(10)
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
                Tables\Columns\TextColumn::make('total_sold')
                    ->label('Units Sold')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Current Stock')
                    ->sortable()
                    ->numeric()
                    ->color(fn (Product $record): string => $record->stock < 10 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money()
                    ->sortable(),
            ])
            ->defaultSort('total_sold', 'desc');
    }
}