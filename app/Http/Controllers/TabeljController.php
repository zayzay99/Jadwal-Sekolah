<?php

namespace App\Http\Controllers;

use App\Models\Tabelj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TabeljController extends Controller
{
    public function index()
    {
        $tabeljs = Tabelj::orderBy('jam_mulai')->get();
        return view('dashboard.tabelj.index', compact('tabeljs'));
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

    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jam_masuk_sekolah' => 'required|date_format:H:i',
            'durasi_pelajaran' => 'required|integer|min:1',
            'jumlah_pelajaran' => 'required|integer|min:1',
            'durasi_istirahat' => 'nullable|integer|min:0',
            'istirahat_setelah_jam_ke' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            Tabelj::truncate(); // Clear old time slots

            $startTime = Carbon::createFromFormat('H:i', $request->jam_masuk_sekolah);
            $lessonDuration = (int) $request->durasi_pelajaran;
            $numberOfLessons = (int) $request->jumlah_pelajaran;
            $breakDuration = (int) $request->durasi_istirahat;
            $breakAfterLesson = (int) $request->istirahat_setelah_jam_ke;

            $timeSlots = [];

            for ($i = 1; $i <= $numberOfLessons; $i++) {
                $endTime = $startTime->copy()->addMinutes($lessonDuration);
                $timeSlots[] = Tabelj::create([
                    'jam_mulai' => $startTime->format('H:i'),
                    'jam_selesai' => $endTime->format('H:i'),
                    'jam' => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
                ]);
                $startTime = $endTime;

                if ($breakDuration && $breakAfterLesson && $i == $breakAfterLesson) {
                    $startTime->addMinutes($breakDuration);
                }
            }

            return response()->json(['success' => true, 'message' => 'Slot waktu berhasil dibuat!', 'timeSlots' => $timeSlots]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat slot waktu: ' . $e->getMessage()], 500);
        }
    }
}
