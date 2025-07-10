<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlySalesWidget extends ChartWidget
{
    protected static ?string $heading = 'Monthly Sales (Last 12 Months)';
    
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 11; $i >= 0; $i--) 
        {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $sales = Sale::whereYear('date', $date->year)
                        ->whereMonth('date', $date->month)
                        ->sum('total');
            $data[] = $sales;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Monthly Sales',
                    'data'  => $data,
                    'backgroundColor' => [
                        '#ef4444', '#f97316', '#f59e0b', '#eab308',
                        '#84cc16', '#22c55e', '#10b981', '#14b8a6',
                        '#06b6d4', '#0ea5e9', '#3b82f6', '#6366f1'
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}