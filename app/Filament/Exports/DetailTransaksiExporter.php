<?php

namespace App\Filament\Exports;

use App\Models\DetailTransaksi;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;

class DetailTransaksiExporter extends Exporter
{
    protected static ?string $model = DetailTransaksi::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('transaksi.user.name')
                ->label('Nama Pengguna'),
            ExportColumn::make('transaksi.produk.name')
                ->label('Nama Produk'),
            ExportColumn::make('transaksi.produk.price')
                ->label('Harga Produk'),
            ExportColumn::make('transaksi.jumlah')
                ->label('Jumlah Pembelian'),
            ExportColumn::make('transaksi.total')
                ->label('Total Harga'),
            ExportColumn::make('transaksi.tanggal_transaksi')
                ->label('Tanggal Pembelian'),
            ExportColumn::make('lunas')
                ->label('Status Pembayaran')
                ->formatStateUsing(function ($state) {
                    return $state == 1 ? 'Lunas' : 'Belum Lunas';
                }),
            ExportColumn::make('tanggal_bayar')
                ->label('Tanggal Bayar'),
            ExportColumn::make('tanggal_tempo')
                ->label('Jatuh Tempo'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your detail transaksi export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public function getXlsxHeaderCellStyle(): ?Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontItalic()
            ->setFontSize(12)
            ->setFontName('Consolas')
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor(Color::ORANGE)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    }
}
