<?php

namespace App\Filament\Widgets;

use App\Models\PurchaseOrder;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PaymentRemindersWidget extends BaseWidget
{
    protected static ?string $heading = 'Payment Reminders - Pending Purchase Orders';
    
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PurchaseOrder::query()
                    ->where('status', '!=', 'completed')
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('order_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_date')
                    ->label('Order Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('days_pending')
                    ->label('Days Pending')
                    ->getStateUsing(function (PurchaseOrder $record): int 
                    {
                        return (int) now()->diffInDays($record->order_date, false) + 1;
                    })
                    ->color(function (PurchaseOrder $record): string 
                    {
                        $days = (int) now()->diffInDays($record->order_date, false) + 1;
                        if ($days > 30) return 'danger';
                        if ($days > 15) return 'warning';
                        return 'success';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(function (string $state): string 
                    {
                        return match ($state) 
                        {
                            'pending'   => 'warning',
                            'confirmed' => 'info',
                            'shipped'   => 'primary',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Amount')
                    ->money()
                    ->sortable(),
            ])
            ->defaultSort('order_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'confirmed' => 'Confirmed',
                        'shipped'   => 'Shipped',
                        'delivered' => 'Delivered',
                    ]),
            ]);
    }
}