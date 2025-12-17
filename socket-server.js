const express = require("express");
const http = require("http");
const socketIO = require("socket.io");

const app = express();
const server = http.createServer(app);
const io = socketIO(server, {
    cors: {
        origin: "*", // Ganti sesuai domain jika tidak menggunakan localhost
        methods: ["GET", "POST"]
    }
});

// Middleware untuk parsing JSON
app.use(express.json());

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
            socket.emit("winnersUpdated", {
                sessionId: sessionId,
                winners: sessionResults[sessionId]
            });
        }
    });

    // Update pemenang ke semua klien di sesi tertentu
    socket.on("updateWinners", ({ sessionId, winners }) => {
        sessionResults[sessionId] = winners; // Simpan hasil undian
        io.to(sessionId).emit("winnersUpdated", {
            sessionId: sessionId,
            winners: winners
        }); // Kirim ke klien
        console.log(`Winners updated for session ${sessionId}`);
    });
});

// HTTP endpoint untuk update winners (untuk integrasi dengan Laravel)
app.post("/updateWinners", (req, res) => {
    const { sessionId, winners } = req.body;
    
    if (!sessionId || typeof sessionId !== 'string' || sessionId.trim() === '') {
        return res.status(400).json({ error: "sessionId must be a non-empty string" });
    }
    
    if (!Array.isArray(winners)) {
        return res.status(400).json({ error: "winners must be an array" });
    }

    sessionResults[sessionId] = winners; // Simpan hasil undian
    io.to(sessionId).emit("winnersUpdated", {
        sessionId: sessionId,
        winners: winners
    }); // Kirim ke klien
    console.log(`Winners updated via HTTP for session ${sessionId}`);
    
    res.json({ success: true, message: "Winners updated successfully" });
});

server.listen(3000, () => {
    console.log("Socket.IO server berjalan di http://localhost:3000");
});