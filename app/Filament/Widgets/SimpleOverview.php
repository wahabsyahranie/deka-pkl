<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Produk;
use App\Models\Transaksi;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class SimpleOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Customer', User::query()->count()),
            Stat::make('Transaksi', Transaksi::query()->count()),
            Stat::make('Produk', Produk::query()->count()),
        ];
    }
}
