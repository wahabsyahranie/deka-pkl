<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksis';
    protected $fillable = [
        'user_id',
        'produk_id',
        'jumlah',
        'total',
        'tanggal_transaksi'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function produk()
    {
        return $this->belongsTo(produk::class);
    }

    public function detailtransaksi()
    {
        return $this->hasOne(DetailTransaksi::class);
    }
}
