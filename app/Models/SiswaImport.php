<?php

namespace App\Models;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsFailures
{
    use Importable;
    private $importedCount = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Cari kelas berdasarkan 'kelas' dari file Excel
        $kelas = Kelas::where('nama_kelas', $row['kelas'])->first();

        // Jika kelas tidak ditemukan, lewati baris ini
        if (!$kelas) {
            return null;
        }

        $siswa = Siswa::create([
            'nis'               => $row['nis'],
            'nama'              => $row['nama'],
            'email'             => $row['email'] ?? null,
            'password'          => Hash::make($row['nis']), // Password default adalah NIS
            'profile_picture'   => 'Default-Profile.png',
        ]);

        // Lampirkan siswa ke kelas yang ditemukan
        $siswa->kelas()->attach($kelas->id);

        $this->importedCount++;

        return $siswa;
    }

    public function rules(): array
    {
        return [
            'nis' => 'required|unique:siswas,nis',
            'nama' => 'required|string',
            'email' => 'nullable|email|unique:siswas,email',
            'kelas' => 'required|exists:kelas,nama_kelas',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kelas.exists' => 'Kelas dengan nama :input tidak ditemukan di database.',
        ];
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        // This method is called when a row fails validation.
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
