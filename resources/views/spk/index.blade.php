@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar SPK</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>No. SPK</th>
                <th>Tanggal</th>
                <th>No. SO</th>
                <th>Customer</th>
                <th>Status</th>
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
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('spk.create') }}" class="btn btn-primary">Buat SPK Baru</a>
</div>
@endsection
