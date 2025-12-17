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
        $this->sendWinnersToSocketIO($sessionId, $winners);

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
            ->whereNotIn('id', Winner::where('draw_session_id', $sessionId)->pluck('participant_id'))
            ->first();

        Winner::create([
            'draw_session_id' => $sessionId,
            'participant_id' => $newWinner->id,
            'valid' => true,
        ]);

        // Kirim hasil undian terbaru ke server Socket.IO
        $updatedWinners = Winner::where('draw_session_id', $sessionId)
            ->with('participant')
            ->where('valid', true)
            ->get()
            ->map(fn($winner) => [
                'name' => $winner->participant->name,
                'code' => $winner->participant->code,
            ]);

        $this->sendWinnersToSocketIO($sessionId, $updatedWinners);

        return redirect()->route('control')->with('success', 'Pemenang berhasil direroll dan diperbarui.');
    }

    private function sendWinnersToSocketIO($sessionId, $winners)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('http://localhost:3000/updateWinners', [
                'json' => [
                    'sessionId' => $sessionId,
                    'winners' => $winners->map(fn($winner) => [
                        'name' => $winner->name,
                        'code' => $winner->code,
                    ]),
                ],
            ]);
        } catch (\Exception $e) {
            // \Log::error('Error sending winners to Socket.IO: ' . $e->getMessage());
        }
    }
}