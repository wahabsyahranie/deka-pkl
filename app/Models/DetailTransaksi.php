<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksis';
    protected $fillable = [
        'transaksi_id',
        'produk_id',
        'jumlah',
        'lunas',
        'tanggal_tempo',
        'tanggal_bayar'
    ];
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}
