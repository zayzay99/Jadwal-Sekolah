<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Log;
use App\Models\Guru;
use Illuminate\Support\Facades\Hash;
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
        $this->defaultGuruId = Guru::value('id');

        if (!$this->activeTahunAjaranId) {
            throw new \Exception('Impor Gagal: Tidak ada Tahun Ajaran yang aktif. Silakan aktifkan satu tahun ajaran di menu "Tahun Ajaran".');
        }

        if (!$this->defaultGuruId) {
            throw new \Exception('Impor Gagal: Tidak ada data Guru di sistem. Sistem memerlukan minimal satu guru untuk dijadikan Wali Kelas default saat membuat kelas baru.');
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
                // Ambil ID guru pertama sebagai fallback JIKA kelas baru dibuat.
                'guru_id' => $this->defaultGuruId,
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
            'nis'   => 'required|integer',
            'nama'  => 'required|string',
            // Hapus validasi format 'email' pada tahap ini untuk menghindari konflik
            // dengan data yang sudah ada. Logika updateOrCreate sudah cukup untuk menanganinya.
            // Cukup pastikan itu adalah string jika ada.
            'email' => 'nullable|string',
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