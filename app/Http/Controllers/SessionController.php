<?php

namespace App\Http\Controllers;

use App\Models\DrawSession;
use App\Models\Winner;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = DrawSession::all();
        return view('sessions.index', compact('sessions'));
    }

    public function create(Request $request)
    {
        DrawSession::create($request->validate([
            'name' => 'required|string|max:255',
            'number_of_winners' => 'required|integer|min:1',
        ]));

        return redirect()->route('sessions.index');
    }

      public function control()
    {
        // Ambil semua sesi undian dengan jumlah pemenang yang valid
        $sessions = DrawSession::withCount('validWinners')->get();

        return view('control', compact('sessions'));
    }
}