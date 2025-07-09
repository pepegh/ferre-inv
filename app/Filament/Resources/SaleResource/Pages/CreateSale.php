<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Models\Sale;
use App\Filament\Resources\SaleResource;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Carbon;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;
}
