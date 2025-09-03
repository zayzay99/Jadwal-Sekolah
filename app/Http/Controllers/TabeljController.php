<?php

namespace App\Http\Controllers;

use App\Models\Tabelj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TabeljController extends Controller
{
    public function index()
    {
        // This method is not used in the new workflow, but kept for now.
        $tabeljs = Tabelj::orderBy('jam_mulai')->get();
        return view('jadwal.tabel_jadwal', compact('tabeljs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $jam_mulai = $request->input('jam_mulai');
            $jam_selesai = $request->input('jam_selesai');

            $tabelj = Tabelj::create([
                'jam_mulai' => $jam_mulai,
                'jam_selesai' => $jam_selesai,
                'jam' => $jam_mulai . '-' . $jam_selesai,
            ]);

            return response()->json(['success' => true, 'message' => 'Jam berhasil ditambahkan!', 'timeSlot' => $tabelj]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan jam: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Tabelj $tabelj)
    {
        try {
            $tabelj->delete();
            return response()->json(['success' => true, 'message' => 'Jam berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus jam.'], 500);
        }
    }
}
