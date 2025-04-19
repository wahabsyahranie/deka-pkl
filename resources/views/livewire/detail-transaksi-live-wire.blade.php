<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <meta name="application-name" content="{{ config('app.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @filamentStyles
        @vite('resources/css/app.css')
    </head>

    <body>
        {{-- {{ $slot }} --}}
        <div class="grid gap-4 grid-cols-3 grid-rows-3">
            <h2>Nama Pemilik: {{ $record->transaksi->user->name }}</h2>
            <h2>Nama Produk: {{ $record->transaksi->produk->name }}</h2>
            <h2>Jumlah Harga / Pcs: {{ $record->transaksi->produk->price }}</h2>
            <h2>Jumlah Pembelian: {{ $record->transaksi->jumlah }}</h2>
            <h2>Jumlah Total: {{ $record->transaksi->total }}</h2>
            <h2>Status Pembayaran: 
                @if ($record->tanggal_bayar === null)
                    Belum Lunas
                @else
                    Lunas
                @endif
            </h2>
            <h2>Tanggal Pembelian: {{ $record->transaksi->tanggal_transaksi }}</h2>
            <h2>Jatuh Tempo: {{ $record->tanggal_tempo }}</h2>
        </div>
        @filamentScripts
        @vite('resources/js/app.js')
    </body>
</html>