<?php

namespace App\Filament\Resources;

use App\Models\Sale;
use App\Models\Product;
use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->default(function () {
                                $prefix = 'V';
                                $date = Carbon::now();
                                $year = $date->format('y');
                                $month = $date->format('m');
                                $day = $date->format('d');
                                $count = Sale::whereDate('created_at', $date->format('Y-m-d'))->count() + 1;

                                return $prefix . $year . $month . $day . $count;
                            })
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date')
                            ->default(function () {
                                return Carbon::now();
                            }),
                        Forms\Components\TextInput::make('client')
                            ->required()
                            ->maxLength(255),
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
                Forms\Components\Section::make('Detalle de venta')
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
                                        $price = Product::find($state)?->price ?? 0;
                                        $quantity = $get('quantity');
                                        $total = $price * $quantity;
                                        $set('unit_price', $price);
                                        $set('sub_total', $total);
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
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->prefix('Q. ')
                    ->numeric()
                    ->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
