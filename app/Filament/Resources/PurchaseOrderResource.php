<?php

namespace App\Filament\Resources;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Filament\Resources\PurchaseOrderResource\RelationManagers;
use App\Filament\Resources\PurchaseOrderResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(Supplier::all()->pluck('name', 'id')),
                        Forms\Components\TextInput::make('order_number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('order_date'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending'   => 'Pendiente',
                                'complited' => 'Completada',
                                'canceled'  => 'Cancelada',
                            ]),
                        Forms\Components\TextInput::make('total')
                            ->disabled()
                            ->dehydrated(true)
                            ->placeholder(function (Forms\Set $set, Forms\Get $get) {
                                $fields = $get('items');
                                $sum = 0;
                                foreach($fields as $field){
                                    $sum+=$field['sub_total'];
                                }
                                $set('total',$sum);
                                return $sum;
                            }),
                    ]),
                Forms\Components\Section::make('Detalle de compra')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->columns(12)
                            ->live()
                            ->columnSpan('full')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::all()->pluck('name', 'id'))
                                    ->columnSpan(4)
                                    ->searchable()
                                    ->live(onBlur: true)
                                    ->required()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $price = Product::find($state)?->cost_price ?? 0;
                                        $quantity = $get('quantity');
                                        $total = $price * $quantity;
                                        $set('unit_price', $price);
                                        $set('sub_total', $total);

                                        self::updateTotal($get, $set);
                                    }),
                                Forms\Components\TextInput::make('quantity')
                                    ->columnSpan(2)
                                    ->required()
                                    ->minValue(1)
                                    ->default(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $price = $get('unit_price') ?? 0;
                                        $quantity = $state;
                                        $total = $price * $quantity;
                                        $set('sub_total', $total);
                                        self::updateTotal($get, $set);
                                    })
                                    ->numeric(),
                                Forms\Components\TextInput::make('unit_price')
                                    ->columnSpan(3)
                                    ->numeric()
                                    ->live(onBlur: true)
                                    ->prefix('Q')
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $price = $state;
                                        $quantity = $get('quantity');
                                        $total = $price * $quantity;
                                        $set('sub_total', $total);

                                        self::updateTotal($get, $set);
                                    }),
                                Forms\Components\TextInput::make('sub_total')
                                    ->columnSpan(3)
                                    ->numeric()
                                    ->prefix('Q')
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get) {
                                        $quantity = $get('quantity') ?? 1;
                                        $price = $get('unit_price') ?? 0;
                                        $set('sub_total', $price * $quantity);
                                    }),
                            ])
                    ])
                
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    private static function updateTotal(Forms\Get $get, Forms\Set $set)
    {
        $items = $get('items') ?? [];
        $total = collect($items)->sum('sub_total');

        $set('total', $total);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
