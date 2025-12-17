@extends('layouts.app')

@section('content')
<h1>Daftar Sesi Undian</h1>
<table>
    <thead>
        <tr>
            <th>Nama Sesi</th>
            <th>Jumlah Pemenang</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sessions as $session)
        <tr>
            <td>{{ $session->name }}</td>
            <td>{{ $session->number_of_winners }}</td>
            <td>
                <form action="{{ route('draw.start', $session->id) }}" method="POST">
                    @csrf
                    <button type="submit">Mulai Undian</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection