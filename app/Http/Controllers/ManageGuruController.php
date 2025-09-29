<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use App\Models\GuruAvailability;
use App\Models\Tabelj;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ManageGuruController extends Controller
{
    private function calculateUsedMinutes($guruId)
    {
        $jadwals = Jadwal::where('guru_id', $guruId)->whereNull('jadwal_kategori_id')->get();
        $totalMenit = 0;

        foreach ($jadwals as $jadwal) {
            $jamParts = explode(' - ', $jadwal->jam);
            if (count($jamParts) == 2) {
                try {
                    $jamMulai = Carbon::parse($jamParts[0]);
                    $jamSelesai = Carbon::parse($jamParts[1]);
                    $totalMenit += $jamSelesai->diffInMinutes($jamMulai);
                } catch (\Exception $e) {
                    // Abaikan jika format jam tidak valid
                }
            }
        }
        return $totalMenit;
    }

    private function calculateAvailabilityMinutes($availabilities)
    {
        $totalMinutes = 0;
        foreach ($availabilities as $availability) {
            try {
                $jamMulai = Carbon::parse($availability->jam_mulai);
                $jamSelesai = Carbon::parse($availability->jam_selesai);
                $totalMinutes += $jamSelesai->diffInMinutes($jamMulai);
            } catch (\Exception $e) {
                // Log or handle invalid time format if necessary
            }
        }
        return $totalMinutes;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $activeTahunAjaranId = session('tahun_ajaran_id');
        $query = Guru::query()->where('tahun_ajaran_id', $activeTahunAjaranId);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('pengampu', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $gurus = $query->paginate(10);

        foreach ($gurus as $guru) {
            $usedMinutes = $this->calculateUsedMinutes($guru->id);
            $guru->used_minutes = $usedMinutes; // Tambahkan properti baru
        }

        return view('dashboard.guru_manage.index', compact('gurus', 'search'));
    }

    public function create()
    {
        $kelas = \App\Models\Kelas::all();
        return view('dashboard.guru_manage.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nip' => 'required|unique:gurus',
            'pengampu' => 'required',
            'email' => 'required|email|unique:gurus',
            'password' => 'required|min:6',
            'max_jp_per_minggu' => 'nullable|integer|min:0',
            'max_jp_per_hari' => 'nullable|integer|min:0',
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'total_jam_mengajar' => 'required|integer|min:0',
        ]);

        $data = $request->all();

        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $name = 'profile-pictures/' . time().'.'.$image->getClientOriginalExtension();
            $image->storeAs('public', $name);
            $data['profile_picture'] = $name;
        } else {
            $data['profile_picture'] = 'Default-Profile.png';
        }

        $data['password'] = Hash::make($request->password);
        $data['sisa_jam_mengajar'] = $request->total_jam_mengajar;

        Guru::create($data);

        return redirect()->route('manage.guru.index')->with('success', 'Guru berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $guru = Guru::findOrFail($id);
        return view('dashboard.guru_manage.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'nip' => 'required|unique:gurus,nip,'.$id,
            'pengampu' => 'required',
            'email' => 'required|email|unique:gurus,email,'.$id,
            'max_jp_per_minggu' => 'nullable|integer|min:0',
            'max_jp_per_hari' => 'nullable|integer|min:0',
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'total_jam_mengajar' => 'required|integer|min:0',
        ]);

        $guru = Guru::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $name = 'profile-pictures/' . time().'.'.$image->getClientOriginalExtension();
            $image->storeAs('public', $name);
            $data['profile_picture'] = $name;
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        // When total_jam_mengajar is updated, sisa_jam_mengajar should reset to the new total_jam_mengajar
        $data['sisa_jam_mengajar'] = $data['total_jam_mengajar'];

        $guru->update($data);

        return redirect()->route('manage.guru.index')->with('success', 'Guru berhasil diupdate!');
    }

    public function destroy($id)
    {
        Guru::destroy($id);
        return redirect()->route('manage.guru.index')->with('success', 'Guru berhasil dihapus!');
    }

    public function editAvailability($id)
    {
        $guru = Guru::findOrFail($id);
        $availabilities = GuruAvailability::where('guru_id', $id)->get()->groupBy('hari')->map(function ($item) {
            return $item->pluck('jam')->toArray();
        });
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $timeSlots = Tabelj::orderBy('jam_mulai')->get();

        return view('dashboard.guru_manage.availability', compact('guru', 'availabilities', 'days', 'timeSlots'));
    }

    public function updateAvailability(Request $request, $id)
    {
        $request->validate([
            'availability' => 'nullable|array',
        ]);

        $guru = Guru::findOrFail($id);

        // Calculate old availability minutes before deleting
        $oldAvailabilities = GuruAvailability::where('guru_id', $id)->get();
        $oldAvailabilityMinutes = $this->calculateAvailabilityMinutes($oldAvailabilities);

        GuruAvailability::where('guru_id', $id)->delete();

        $newAvailabilitiesData = [];
        if ($request->has('availability')) {
            foreach ($request->availability as $hari => $jams) {
                foreach ($jams as $jamString) {
                    $jamParts = explode(' - ', $jamString);
                    if (count($jamParts) == 2) {
                        try {
                            $newAvailabilitiesData[] = [
                                'guru_id' => $id,
                                'hari' => $hari,
                                'jam_mulai' => trim($jamParts[0]),
                                'jam_selesai' => trim($jamParts[1]),
                            ];
                        } catch (\Exception $e) {
                            // Log or handle invalid time format if necessary
                        }
                    }
                }
            }
            GuruAvailability::insert($newAvailabilitiesData); // Use insert for bulk creation
        }

        // Calculate new availability minutes
        // For simplicity, let's retrieve them again from DB after insert
        $newAvailabilities = GuruAvailability::where('guru_id', $id)->get();
        $newAvailabilityMinutes = $this->calculateAvailabilityMinutes($newAvailabilities);

        // Calculate the change in availability minutes
        $changeInAvailabilityMinutes = $newAvailabilityMinutes - $oldAvailabilityMinutes;

        // Update sisa_jam_mengajar
        // If new availability is more, sisa_jam_mengajar decreases (more hours are "reserved")
        // If new availability is less, sisa_jam_mengajar increases (fewer hours are "reserved")
        $guru->sisa_jam_mengajar -= $changeInAvailabilityMinutes;
        $guru->save();

        return redirect()->route('manage.guru.index')->with('success', 'Ketersediaan guru berhasil diperbarui!');
    }
}