<?php

namespace App\Http\Controllers;

use App\Models\Tabelj;
use App\Models\JadwalKategori;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TabeljController extends Controller
{
    public function index()
    {
        $tabeljs = Tabelj::with('jadwalKategori')->orderBy('jam_mulai')->get();
        return view('dashboard.tabelj.index', compact('tabeljs'));
    }

    public function create()
    {
        return view('dashboard.tabelj.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jam_mulai' => 'required|date_format:H:i',
            'jumlah_jam_pelajaran' => 'required|integer|min:1',
            'durasi' => 'required|integer|min:1',
            'istirahat_setelah_jam_ke' => 'nullable|array',
            'istirahat_setelah_jam_ke.*' => 'required|integer|min:1',
            'durasi_istirahat_menit' => 'nullable|array',
            'durasi_istirahat_menit.*' => 'required|integer|min:1',
            'keterangan_istirahat' => 'nullable|array',
            'keterangan_istirahat.*' => 'nullable|string|max:255',
        ]);

        $startTime = Carbon::createFromTimeString($request->jam_mulai);
        $lessonCount = (int)$request->jumlah_jam_pelajaran;
        $duration = (int)$request->durasi;

        $breaks = [];
        if ($request->has('istirahat_setelah_jam_ke')) {
            foreach ($request->istirahat_setelah_jam_ke as $index => $jamKe) {
                if (isset($request->durasi_istirahat_menit[$index])) {
                    $breaks[$jamKe] = [
                        'duration' => (int)$request->durasi_istirahat_menit[$index],
                        'description' => $request->keterangan_istirahat[$index] ?? 'Istirahat'
                    ];
                }
            }
        }
        ksort($breaks);

        $istirahatKategori = JadwalKategori::firstOrCreate(['nama_kategori' => 'Istirahat']);
        $currentTime = $startTime->copy();
        $generatedCount = 0;

        if ($request->has('replace_existing')) {
            // Clear existing slots before generating new ones
            Tabelj::truncate();
        }

        for ($i = 1; $i <= $lessonCount; $i++) {
            $slotEndTime = $currentTime->copy()->addMinutes($duration);

            Tabelj::create([
                'jam_mulai' => $currentTime->format('H:i'),
                'jam_selesai' => $slotEndTime->format('H:i'),
                'jam' => $currentTime->format('H:i') . ' - ' . $slotEndTime->format('H:i'),
            ]);

            $generatedCount++;
            $currentTime = $slotEndTime->copy();

            // Check for break after this lesson
            if (isset($breaks[$i])) {
                $breakDuration = $breaks[$i]['duration'];
                $breakDescription = $breaks[$i]['description'];
                $breakEndTime = $currentTime->copy()->addMinutes($breakDuration);

                Tabelj::create([
                    'jam_mulai' => $currentTime->format('H:i'),
                    'jam_selesai' => $breakEndTime->format('H:i'),
                    'jam' => $breakDescription,
                    'jadwal_kategori_id' => $istirahatKategori->id,
                ]);

                $currentTime = $breakEndTime->copy();
                $generatedCount++;
            }
        }

        if ($generatedCount > 0) {
            return redirect()->route('manage.tabelj.index')->with('success', "Berhasil generate {$generatedCount} slot waktu.");
        } else {
            return back()->with('error', 'Tidak ada slot waktu yang dapat digenerate dengan pengaturan yang diberikan.')->withInput();
        }
    }

    public function edit(Tabelj $tabelj)
    {
        $kategoris = JadwalKategori::all();
        return view('dashboard.tabelj.edit', compact('tabelj', 'kategoris'));
    }

    public function update(Request $request, Tabelj $tabelj)
    {
        $request->validate([
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'jadwal_kategori_id' => 'required|exists:jadwal_kategoris,id',
        ]);

        $tabelj->update([
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'jam' => $request->jam_mulai . ' - ' . $request->jam_selesai,
            'jadwal_kategori_id' => $request->jadwal_kategori_id,
        ]);

        return redirect()->route('manage.tabelj.index')->with('success', 'Slot waktu berhasil diperbarui.');
    }

    public function destroy(Tabelj $tabelj)
    {
        // Optional: Add validation to check if this timeslot is currently in use in any schedule.
        // For now, we will just delete it.
        $tabelj->delete();

        return redirect()->route('manage.tabelj.index')->with('success', 'Slot waktu berhasil dihapus.');
    }

    public function destroyAll()
    {
        Tabelj::truncate();
        return redirect()->route('manage.tabelj.index')->with('success', 'Semua slot waktu berhasil dihapus.');
    }

    public function assignCategory()
    {
        $tabeljs = Tabelj::orderBy('jam_mulai')->get();
        $kategoris = JadwalKategori::all();
        return view('dashboard.tabelj.assign_category', compact('tabeljs', 'kategoris'));
    }

    public function storeAssignedCategory(Request $request)
    {
        $request->validate([
            'selected_slots' => 'required|array',
            'selected_slots.*' => 'exists:tabeljs,id',
            'jadwal_kategori_id' => 'required|exists:jadwal_kategoris,id',
        ]);

        Tabelj::whereIn('id', $request->selected_slots)->update([
            'jadwal_kategori_id' => $request->jadwal_kategori_id,
        ]);

        return redirect()->route('manage.tabelj.index')->with('success', 'Kategori berhasil ditetapkan ke slot waktu yang dipilih.');
    }

    public function addBreak(Request $request, Tabelj $tabelj)
    {
        $request->validate([
            'durasi_istirahat' => 'required|integer|min:1',
        ]);

        $breakDuration = (int)$request->input('durasi_istirahat');
        
        // Find all slots that start at or after the current one ends.
        $subsequentSlots = Tabelj::where('jam_mulai', '>=', $tabelj->jam_selesai)
                                  ->orderBy('jam_mulai')
                                  ->get();

        // Shift subsequent slots
        // We iterate in reverse to avoid unique constraint violations if times overlap.
        foreach ($subsequentSlots->reverse() as $slot) {
            $newStartTime = Carbon::parse($slot->jam_mulai)->addMinutes($breakDuration);
            $newEndTime = Carbon::parse($slot->jam_selesai)->addMinutes($breakDuration);
            $slot->update([
                'jam_mulai' => $newStartTime->format('H:i:s'),
                'jam_selesai' => $newEndTime->format('H:i:s'),
                'jam' => $newStartTime->format('H:i') . ' - ' . $newEndTime->format('H:i'),
            ]);
        }

        // Now, create the break slot
        $breakStartTime = Carbon::parse($tabelj->jam_selesai);
        $breakEndTime = $breakStartTime->copy()->addMinutes($breakDuration);

        $istirahatKategori = JadwalKategori::firstOrCreate(['nama_kategori' => 'Istirahat']);

        Tabelj::create([
            'jam_mulai' => $breakStartTime->format('H:i:s'),
            'jam_selesai' => $breakEndTime->format('H:i:s'),
            'jam' => $breakStartTime->format('H:i') . ' - ' . $breakEndTime->format('H:i'),
            'jadwal_kategori_id' => $istirahatKategori->id,
        ]);

        return redirect()->route('manage.tabelj.index')->with('success', 'Jam istirahat berhasil ditambahkan.');
    }
}
