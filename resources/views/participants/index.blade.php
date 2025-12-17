@extends('layouts.app')

@section('content')
<h1>Daftar Peserta</h1>
<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Kode</th>
        </tr>
    </thead>
    <tbody>
        @foreach($participants as $participant)
        <tr>
            <td>{{ $participant->name }}</td>
            <td>{{ $participant->code }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection