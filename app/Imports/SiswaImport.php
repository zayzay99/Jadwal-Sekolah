<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Log;
use App\Models\Guru;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SiswaImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading
{
    use Importable, SkipsFailures;

    private int $importedCount = 0;
    private $activeTahunAjaranId;
    private $defaultGuruId;

    public function __construct()
    {
        // Ambil tahun ajaran aktif saat objek dibuat agar tidak query berulang kali
        $this->activeTahunAjaranId = TahunAjaran::where('is_active', true)->value('id');

        // Ambil ID guru pertama sebagai fallback jika kelas baru perlu dibuat
        $this->defaultGuruId = Guru::value('id');

        // Hentikan proses impor dari awal jika tidak ada prasyarat yang terpenuhi.
        // Ini akan memberikan pesan error yang jelas daripada gagal di tengah jalan.
        if (!$this->activeTahunAjaranId) {
            throw new \Exception('Impor Gagal: Tidak ada Tahun Ajaran yang aktif. Silakan aktifkan satu tahun ajaran di menu "Tahun Ajaran".');
        }
        if (!$this->defaultGuruId) {
            throw new \Exception('Impor Gagal: Tidak ada data Guru di sistem. Silakan tambahkan minimal satu guru untuk dijadikan Wali Kelas default.');
        }
    }

    /**
     * @param Row $row
     */
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $rowData = $row->toArray();

        // Cari kelas berdasarkan nama di tahun ajaran aktif.
        // Jika tidak ada, buat kelas baru secara otomatis.
        $kelas = Kelas::firstOrCreate(
            [
                'nama_kelas' => $rowData['kelas'],
                'tahun_ajaran_id' => $this->activeTahunAjaranId,
            ],
            [
                'guru_id' => $this->defaultGuruId, // Hanya digunakan jika kelas baru dibuat
            ]
        );

        // Gunakan updateOrCreate untuk menghindari error duplikat jika proses diulang
        $siswa = Siswa::updateOrCreate(
            ['nis' => $rowData['nis']], // Cari siswa berdasarkan NIS
            [ // Data untuk diupdate atau dibuat
            'nama'            => $rowData['nama'],
            'email'           => $rowData['email'] ?? null,
            'password'        => Hash::make($rowData['nis']), // Password default = NIS
            'profile_picture' => 'Default-Profile.png',
        ]);

        // Lampirkan siswa ke kelas DENGAN tahun ajaran yang benar
        // sync() akan menghapus relasi lama (di tahun ajaran ini) dan menambahkan yang baru.
        $siswa->kelas()->sync([$kelas->id => ['tahun_ajaran_id' => $this->activeTahunAjaranId]]);

        $this->importedCount++;
    }

    public function rules(): array
    {
        return [
            'nis'   => 'required|string',
            'nama'  => 'required|string',
            'email' => 'nullable|string', // Hanya validasi bahwa email adalah string (jika ada), bukan format email.
            'kelas' => 'required|string',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        // Log setiap kegagalan validasi untuk debugging
        foreach ($failures as $failure) {
            Log::error('Import Gagal Validasi', [
                'baris' => $failure->row(),
                'atribut' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ]);
        }
    }

    public function chunkSize(): int { return 100; }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}