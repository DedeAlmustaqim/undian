<?php

namespace App\Http\Controllers;

use App\Models\DrawSession;
use App\Models\Participant;
use App\Models\Winner;
use Illuminate\Http\Request;

class DrawController extends Controller
{
    public function start(Request $request, $sessionId)
    {
        $session = DrawSession::findOrFail($sessionId);
        $numberOfWinners = $session->number_of_winners;

        // Pilih pemenang acak
        $winners = Participant::inRandomOrder()->limit($numberOfWinners)->get();

        // Simpan hasil undian ke database
        foreach ($winners as $winner) {
            Winner::create([
                'draw_session_id' => $sessionId,
                'participant_id' => $winner->id,
                'valid' => true,
            ]);
        }

        // Kirim hasil undian ke server Socket.IO
        $this->sendWinnersToSocketIO($sessionId, $this->transformWinnersData($winners));

        // Redirect ke halaman sesi undian atau hasil
        return redirect()->route('sessions.index')->with('success', 'Undian berhasil dimulai.');
    }

    public function reroll(Request $request, $sessionId)
    {
        // Validasi input
        $request->validate([
            'winner_id' => 'required|integer|exists:winners,id',
        ]);

        // Tandai pemenang lama tidak valid
        $winner = Winner::find($request->winner_id);
        $winner->update(['valid' => false]);

        // Pilih pemenang baru
        $newWinner = Participant::inRandomOrder()
            ->whereNotIn('id', Winner::forSession($sessionId)->pluck('participant_id'))
            ->first();

        Winner::create([
            'draw_session_id' => $sessionId,
            'participant_id' => $newWinner->id,
            'valid' => true,
        ]);

        // Kirim hasil undian terbaru ke server Socket.IO
        $updatedWinners = $this->getValidWinners($sessionId);

        $this->sendWinnersToSocketIO($sessionId, $this->transformWinnersData($updatedWinners));

        return redirect()->route('control')->with('success', 'Pemenang berhasil direroll dan diperbarui.');
    }

    private function getValidWinners($sessionId)
    {
        return Winner::forSession($sessionId)
            ->valid()
            ->with('participant')
            ->get()
            ->pluck('participant');
    }

    private function transformWinnersData($winners)
    {
        return $winners->map(fn($winner) => [
            'name' => $winner->name,
            'code' => $winner->code,
        ]);
    }

    private function sendWinnersToSocketIO($sessionId, $winners)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $socketUrl = env('SOCKETIO_URL', 'http://localhost:3000');
            $response = $client->post($socketUrl . '/updateWinners', [
                'json' => [
                    'sessionId' => $sessionId,
                    'winners' => $winners,
                ],
            ]);
        } catch (\Exception $e) {
            // \Log::error('Error sending winners to Socket.IO: ' . $e->getMessage());
        }
    }
}