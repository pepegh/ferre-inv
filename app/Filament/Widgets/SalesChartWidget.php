<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Daily Sales (Last 30 Days)';
    
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $data   = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date     = Carbon::now()->subDays($i);
            $labels[] = $date->format('M j');
            
            $sales  = Sale::whereDate('date', $date)->sum('total');
            $data[] = $sales;
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Sales Amount',
                    'data'            => $data,
                    'borderColor'     => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}