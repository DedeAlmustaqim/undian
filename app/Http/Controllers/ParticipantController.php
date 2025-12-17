<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index()
    {
        $participants = Participant::all();
        return view('participants.index', compact('participants'));
    }

    public function create(Request $request)
    {
        Participant::create($request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:participants',
        ]));

        return redirect()->route('participants.index');
    }
}