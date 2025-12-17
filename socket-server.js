const io = require("socket.io")(3000, {
    cors: {
        origin: "*", // Ganti sesuai domain jika tidak menggunakan localhost
        methods: ["GET", "POST"]
    }
});

let sessionResults = {};

// Setup koneksi Socket.IO
io.on("connection", (socket) => {
    console.log("Client connected: " + socket.id);

    // Join session tertentu berdasarkan session ID
    socket.on("joinSession", (sessionId) => {
        socket.join(sessionId);
        console.log(`Client ${socket.id} joined session ${sessionId}`);

        // Jika hasil undian sudah tersedia, kirim langsung ke klien
        if (sessionResults[sessionId]) {
            socket.emit("winnersUpdated", sessionResults[sessionId]);
        }
    });

    // Update pemenang ke semua klien di sesi tertentu
    socket.on("updateWinners", ({ sessionId, winners }) => {
        sessionResults[sessionId] = winners; // Simpan hasil undian
        io.to(sessionId).emit("winnersUpdated", winners); // Kirim ke klien
    });
});

console.log("Socket.IO server berjalan di http://localhost:3000");