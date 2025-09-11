<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ManageGuruController extends Controller
{
    private function calculateTotalJP($guruId)
    {
        $jadwals = Jadwal::where('guru_id', $guruId)->whereNull('jadwal_kategori_id')->get();
        $totalJP = 0;

        foreach ($jadwals as $jadwal) {
            $jamParts = explode(' - ', $jadwal->jam);
            if (count($jamParts) == 2) {
                try {
                    $jamMulai = Carbon::parse($jamParts[0]);
                    $jamSelesai = Carbon::parse($jamParts[1]);
                    $durasiMenit = $jamSelesai->diffInMinutes($jamMulai);
                    $totalJP += floor($durasiMenit / 35); // Asumsi 1 JP = 35 menit
                } catch (\Exception $e) {
                    // Abaikan jika format jam tidak valid
                }
            }
        }
        return $totalJP;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Guru::query();
        
        $gurus = $query->paginate(10);

        foreach ($gurus as $guru) {
            $guru->total_jp = $this->calculateTotalJP($guru->id);
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
        ]);

        $data = $request->all();

        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/img');
            $image->move($destinationPath, $name);
            $data['profile_picture'] = $name;
        }

        $data['password'] = Hash::make($request->password);

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
        ]);

        $guru = Guru::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/img');
            $image->move($destinationPath, $name);
            $data['profile_picture'] = $name;
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $guru->update($data);

        return redirect()->route('manage.guru.index')->with('success', 'Guru berhasil diupdate!');
    }

    public function destroy($id)
    {
        Guru::destroy($id);
        return redirect()->route('manage.guru.index')->with('success', 'Guru berhasil dihapus!');
    }
}
