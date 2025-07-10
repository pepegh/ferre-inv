<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SalesStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $todaySales = Sale::whereDate('date', today())->sum('total');
        $monthSales = Sale::whereMonth('date', now()->month)
                         ->whereYear('date', now()->year)
                         ->sum('total');
        
        $lastMonthSales = Sale::whereMonth('date', now()->subMonth()->month)
                             ->whereYear('date', now()->subMonth()->year)
                             ->sum('total');
        
        $monthGrowth = $lastMonthSales > 0 
            ? (($monthSales - $lastMonthSales) / $lastMonthSales) * 100 
            : ($monthSales > 0 ? 100 : 0);
        
        $growthDescription = $lastMonthSales == 0 && $monthSales > 0 
            ? 'First sales this period!' 
            : ($monthGrowth >= 0 ? number_format(abs($monthGrowth), 1) . '% increase' : number_format(abs($monthGrowth), 1) . '% decrease');

        $lowStockCount = Product::where('stock', '<', 10)->count();
        $pendingOrders = PurchaseOrder::whereNotIn('status', ['completed', 'cancelled'])->count();

        return [
            Stat::make('Today\'s Sales', '$' . number_format($todaySales, 2))
                ->description('Sales made today')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
                
            Stat::make('This Month\'s Sales', '$' . number_format($monthSales, 2))
                ->description($growthDescription)
                ->descriptionIcon($monthGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthGrowth >= 0 ? 'success' : 'danger'),
                
            Stat::make('Low Stock Items', $lowStockCount)
                ->description('Products below 10 units')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'warning' : 'success'),
                
            Stat::make('Pending Orders', $pendingOrders)
                ->description('Purchase orders to process')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'info' : 'success'),
        ];
    }
}