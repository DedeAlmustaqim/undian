<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Display</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>
<body class="bg-gray-800 text-white">
    <div class="container mx-auto text-center py-10">
        <h1 class="text-4xl font-bold text-yellow-400">Hasil Undian</h1>
        <h2 id="sessionName" class="text-2xl mt-4 text-gray-400">Sesi ID: {{ $sessionId }}</h2>

        <div id="winner-list" class="mt-10 space-y-4">
            <p class="text-xl text-gray-300">Menunggu hasil undian...</p>
        </div>
    </div>

    @include('partials.socketio-config')
    
    <script>
        const sessionId = "{{ $sessionId }}"; // Ambil session ID dari Laravel

        // Masuk ke sesi undian
        socket.emit("joinSession", sessionId);

        // Dengarkan update pemenang
        socket.on("winnersUpdated", (winners) => {
            const winnerList = document.getElementById("winner-list");
            winnerList.innerHTML = ""; // Clear list sebelumnya

            winners.forEach(winner => {
                const winnerItem = document.createElement("div");
                winnerItem.className = "p-4 bg-gray-900 rounded-lg shadow-lg";

                const winnerName = document.createElement("h3");
                winnerName.className = "text-2xl font-bold text-yellow-400";
                winnerName.textContent = winner.name;

                const winnerCode = document.createElement("p");
                winnerCode.className = "text-lg text-gray-400";
                winnerCode.textContent = `Kode: ${winner.code}`;

                winnerItem.appendChild(winnerName);
                winnerItem.appendChild(winnerCode);

                winnerList.appendChild(winnerItem);
            });
        });
    </script>
</body>
</html>