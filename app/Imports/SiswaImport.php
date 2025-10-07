<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsFailures;

    private int $importedCount = 0;
    private $activeTahunAjaranId;

    public function __construct()
    {
        // Ambil tahun ajaran aktif saat objek dibuat agar tidak query berulang kali
        $this->activeTahunAjaranId = TahunAjaran::where('is_active', true)->value('id');
    }

    public function model(array $row)
    {
        // Cari kelas berdasarkan 'nama_kelas' DAN tahun ajaran yang aktif
        $kelas = Kelas::where('nama_kelas', $row['kelas'])
                      ->where('tahun_ajaran_id', $this->activeTahunAjaranId)
                      ->first();

        // Jika kelas tidak ditemukan, lewati baris ini
        if (!$kelas) {
            return null;
        }

        $siswa = Siswa::create([
            'nis'             => $row['nis'],
            'nama'            => $row['nama'],
            'email'           => $row['email'] ?? null,
            'password'        => Hash::make($row['nis']), // Password default = NIS
            'profile_picture' => 'Default-Profile.png',
        ]);

        // Lampirkan siswa ke kelas DENGAN tahun ajaran yang benar
        $siswa->kelas()->attach($kelas->id, ['tahun_ajaran_id' => $this->activeTahunAjaranId]);

        $this->importedCount++;

        return $siswa;
    }

    public function rules(): array
    {
        return [
            'nis'   => 'required|unique:siswas,nis',
            'nama'  => 'required|string',
            'email' => 'nullable|email|unique:siswas,email',
            'kelas' => 'required|string', // Validasi 'exists' kita lakukan manual di dalam method model() agar lebih akurat
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kelas.exists' => 'Kelas dengan nama :input tidak ditemukan di database.',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        // Method ini akan dipanggil jika ada baris yang gagal validasi
    }

    public function batchSize(): int { return 100; }
    public function chunkSize(): int { return 100; }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
