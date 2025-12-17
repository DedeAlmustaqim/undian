@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-center mb-8">Control Panel Pengundian</h1>
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="px-4 py-2 border-b">Sesi</th>
                <th class="px-4 py-2 border-b">Jumlah Pemenang</th>
                <th class="px-4 py-2 border-b">Pemenang Saat Ini</th>
                <th class="px-4 py-2 border-b">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sessions as $session)
                <tr>
                    <td class="px-4 py-2 border-b">{{ $session->name }}</td>
                    <td class="px-4 py-2 border-b text-center">{{ $session->number_of_winners }}</td>
                    <td class="px-4 py-2 border-b text-center" id="winner-count-session-{{ $session->id }}">{{ $session->valid_winners_count }}</td>
                    <td class="px-4 py-2 border-b text-center">
                        <!-- Tombol Mulai Undian -->
                        <form action="{{ route('draw.start', $session->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Mulai Undian</button>
                        </form>

                        <!-- Tombol Reroll -->
                        <button onclick="openRerollForm({{ $session->id }})" 
                                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Reroll
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal untuk Reroll -->
    <div id="rerollModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-10">
        <div class="bg-white p-8 rounded shadow-lg w-96 mx-auto my-32">
            <h2 class="text-xl font-bold mb-4">Reroll Pemenang</h2>
            <form id="rerollForm" method="POST">
                @csrf
                <p class="mb-4">Masukkan ID Pemenang yang akan dihapus:</p>
                <input type="number" name="winner_id" id="winnerIdInput" class="border p-2 w-full mb-4" placeholder="ID Pemenang" required>
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Reroll</button>
                <button type="button" onclick="closeRerollModal()" 
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Batal
                </button>
            </form>
        </div>
    </div>

    <script>
        // Fungsi membuka modal reroll
        function openRerollForm(sessionId) {
            const modal = document.getElementById('rerollModal');
            const form = document.getElementById('rerollForm');
            modal.classList.remove('hidden');
            form.action = `/reroll/${sessionId}`;
        }

        // Fungsi menutup modal reroll
        function closeRerollModal() {
            const modal = document.getElementById('rerollModal');
            const input = document.getElementById('winnerIdInput');
            modal.classList.add('hidden');
            input.value = ""; // Reset input
        }
    </script>

    @include('partials.socketio-config')
    
    <script>
        @foreach ($sessions as $session)
            // Dengarkan pemenang terbaru untuk setiap sesi
            socket.on('winnersUpdated', (data) => {
                if (data.sessionId === {{ $session->id }}) {
                    // Perbarui jumlah pemenang di tabel
                    document.getElementById('winner-count-session-{{ $session->id }}').innerText = data.winners.length;
                }
            });
        @endforeach
    </script>
@endsection