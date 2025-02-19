@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail SPK</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">No. SPK: {{ $spk->no_spk }}</h5>
            <p class="card-text"><strong>Tanggal:</strong> {{ $spk->tanggal }}</p>
            <p class="card-text"><strong>No. SO:</strong> {{ $spk->no_so }}</p>
            <p class="card-text"><strong>Teknisi 1:</strong> {{ $spk->teknisi_1 }}</p>
            <p class="card-text"><strong>Teknisi 2:</strong> {{ $spk->teknisi_2 ?? '-' }}</p>
            <p class="card-text"><strong>Customer:</strong> {{ $spk->customer }}</p>
            <p class="card-text"><strong>Alamat:</strong> {{ $spk->alamat }}</p>
            <p class="card-text"><strong>No. HP:</strong> {{ $spk->no_hp }}</p>
            <p class="card-text"><strong>Jenis Mobil:</strong> {{ $spk->jenis_mobil }}</p>
            <p class="card-text"><strong>No. Plat:</strong> {{ $spk->no_plat }}</p>
            <p class="card-text"><strong>Nama Barang:</strong> {{ $spk->nama_barang }}</p>
            <p class="card-text"><strong>Qty:</strong> {{ $spk->qty }}</p>
            <p class="card-text"><strong>Catatan:</strong> {{ $spk->catatan ?? '-' }}</p>
            <p class="card-text"><strong>Status:</strong> {{ $spk->status }}</p>
        </div>
    </div>
</div>
@endsection
