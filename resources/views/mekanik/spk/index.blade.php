@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar SPK Baru</h1>
    <table class="table">
        <thead>
            <tr>
                <th>No. SPK</th>
                <th>Tanggal</th>
                <th>No. SO</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($spks as $spk)
            <tr>
                <td>{{ $spk->no_spk }}</td>
                <td>{{ $spk->tanggal }}</td>
                <td>{{ $spk->no_so }}</td>
                <td>{{ $spk->customer }}</td>
                <td>{{ $spk->status }}</td>
                <td>
                    <a href="{{ route('mekanik.spk.show', $spk->id) }}" class="btn btn-primary">Lihat Detail</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
