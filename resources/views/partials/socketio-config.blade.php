{{-- Socket.IO Configuration Partial --}}
<script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
<script>
    // Konfigurasi Socket.IO
    const socket = io.connect("{{ env('SOCKETIO_URL', 'http://localhost:3000') }}");
</script>
