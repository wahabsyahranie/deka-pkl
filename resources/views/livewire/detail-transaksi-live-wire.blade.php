<div>
    <hr>
    <h2>Nama Pemilik: {{ $record->transaksi->user->name }}</h2>
    <h2>Nama Produk: {{ $record->transaksi->produk->name }}</h2>
    <h2>Jumlah Harga / Pcs: {{ $record->transaksi->produk->price }}</h2>
    <h2>Jumlah Pembelian: {{ $record->transaksi->jumlah }}</h2>
    <h2>Jumlah Total: {{ $record->transaksi->total }}</h2>
    <h2>Tanggal Pembelian: {{ $record->transaksi->tanggal_transaksi }}</h2>
    <h2>Jatuh Tempo: {{ $record->tanggal_tempo }}</h2>
</div>