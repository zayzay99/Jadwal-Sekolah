<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use App\Imports\GuruImport;
use App\Models\GuruAvailability;
use App\Models\Tabelj;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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

        // Start query and filter by the active school year
        $query = Guru::where('tahun_ajaran_id', $activeTahunAjaranId);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('pengampu', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $gurus = $query->paginate(10);
 
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
        'nip' => [
            'required',
            Rule::unique('gurus')->where(function ($query) {
                return $query->where('tahun_ajaran_id', session('tahun_ajaran_id'));
            }),
        ],
        'pengampu' => 'required',
        'email' => [
            'required',
            'email',
            Rule::unique('gurus')->where(function ($query) {
                return $query->where('tahun_ajaran_id', session('tahun_ajaran_id'));
            }),
        ],
        'password' => 'required|min:6',
        'max_jp_per_minggu' => 'nullable|integer|min:0',
        'max_jp_per_hari' => 'nullable|integer|min:0',
        'profile_picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'total_jam_mengajar' => 'required|integer|min:0',
    ]);

    $data = $request->all();
    $data['tahun_ajaran_id'] = session('tahun_ajaran_id');

    // Handle profile picture dengan default value
    if ($request->hasFile('profile_picture')) {
        $image = $request->file('profile_picture');
        $name = 'profile-pictures/' . time().'.'.$image->getClientOriginalExtension();
        $image->storeAs('public', $name);
        $data['profile_picture'] = $name;
    } else {
        // Set default profile picture jika tidak ada file yang diupload
        $data['profile_picture'] = 'default-profile.jpg';
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
        $guru = Guru::findOrFail($id);

        $request->validate([
            'nama' => 'required',
            'nip' => [
                'required',
                Rule::unique('gurus')->where(function ($query) use ($guru) {
                    return $query->where('tahun_ajaran_id', $guru->tahun_ajaran_id);
                })->ignore($id),
            ],
            'pengampu' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('gurus')->where(function ($query) use ($guru) {
                    return $query->where('tahun_ajaran_id', $guru->tahun_ajaran_id);
                })->ignore($id),
            ],
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
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        // Hitung ulang sisa jam mengajar berdasarkan jadwal yang sudah ada
        $totalJamBaru = (int)$data['total_jam_mengajar'];
        $usedMinutes = 0; // Default
        
        // Hanya hitung jam terpakai jika ada tahun ajaran aktif
        if ($guru->tahun_ajaran_id) {
            $usedMinutes = $this->calculateUsedMinutesForYear($guru->id, $guru->tahun_ajaran_id);
        }
        $data['sisa_jam_mengajar'] = $totalJamBaru - $usedMinutes;

        $guru->update($data);

        return redirect()->route('manage.guru.index')->with('success', 'Guru berhasil diupdate!');
    }

    public function destroy($id)
    {
        // Validasi: Jangan hapus guru jika masih punya jadwal di tahun ajaran aktif
        $activeTahunAjaranId = session('tahun_ajaran_id');
        if ($activeTahunAjaranId && Jadwal::where('guru_id', $id)->where('tahun_ajaran_id', $activeTahunAjaranId)->exists()) {
            return redirect()->route('manage.guru.index')->with('error', 'Guru tidak dapat dihapus karena masih memiliki jadwal mengajar.');
        }
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
        $timeSlots = Tabelj::orderBy('jam_mulai')->get()->map(function ($slot) {
            $slot->jam_mulai = Carbon::parse($slot->jam_mulai)->format('H:i');
            $slot->jam_selesai = Carbon::parse($slot->jam_selesai)->format('H:i');
            return $slot;
        });

        // Mengambil semua jadwal yang ada untuk validasi bentrok di frontend
        $activeTahunAjaranId = session('tahun_ajaran_id');
        $existingSchedules = Jadwal::where('tahun_ajaran_id', $activeTahunAjaranId)
            ->whereNotNull('guru_id')
            ->with('kelas:id,nama_kelas', 'guru:id,nama')
            ->get(['guru_id', 'kelas_id', 'hari', 'jam']);

        // Mengorganisir jadwal untuk akses cepat di JavaScript
        $scheduleClashData = [];
        foreach ($existingSchedules as $schedule) {
            $scheduleClashData[$schedule->hari][$schedule->jam][$schedule->guru_id] = $schedule->kelas->nama_kelas ?? 'Kelas tidak diketahui';
        }

        return view('dashboard.guru_manage.availability', compact(
            'guru', 'availabilities', 'days', 'timeSlots', 'scheduleClashData'
        ));
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

        return redirect()->route('manage.guru.index')->with('success', 'Ketersediaan guru berhasil diperbarui!');
    }

    /**
     * Menampilkan form untuk import data guru.
     */
    public function showImportForm()
    {
        // Path ke file blade yang sudah dibuat sebelumnya
        return view('dashboard.guru_manage.import');
    }

    /**
     * Mengimpor data guru dari file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        // Validasi: Pastikan ada tahun ajaran yang aktif di sesi
        if (!session()->has('tahun_ajaran_id')) {
            return back()->withErrors(['file' => 'Tidak ada Tahun Ajaran yang aktif. Silakan pilih atau buat Tahun Ajaran yang aktif terlebih dahulu sebelum mengimpor data.']);
        }

        try {
            $import = new GuruImport;
            Excel::import($import, $request->file('file'));

            // Cek apakah ada baris yang gagal dan tidak ada yang berhasil
            if ($import->failures()->isNotEmpty() && $import->getImportedCount() === 0) {
                return back()->withFailures($import->failures());
            }

            return redirect()->route('manage.guru.index')->with('success', 'Data guru berhasil diimpor!');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             return back()->withFailures($e->failures());
        } catch (\Throwable $th) {
            // Tangkap semua jenis error lain dan catat ke log
            Log::error('Error saat impor guru: ' . $th->getMessage());
            return back()->withErrors(['file' => 'Terjadi error saat proses impor. Silakan cek file log untuk detail. Pesan: ' . $th->getMessage()]);
        }
    }

    

        private function calculateUsedMinutesForYear($guruId, $tahunAjaranId)
    {
        $jadwals = Jadwal::where('guru_id', $guruId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->whereNull('jadwal_kategori_id')->get();
        $totalMenit = 0;

        foreach ($jadwals as $jadwal) {
            $jamParts = explode(' - ', $jadwal->jam);
            if (count($jamParts) == 2) {
                try {
                    $jamMulai = Carbon::parse($jamParts[0]);
                    $jamSelesai = Carbon::parse($jamParts[1]);
                    $totalMenit += $jamSelesai->diffInMinutes($jamMulai);
                } catch (\Exception $e) {}
            }
        }
        return $totalMenit;
    }

}