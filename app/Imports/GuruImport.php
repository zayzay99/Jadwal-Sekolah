<?php

namespace App\Imports;

use App\Models\Guru;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class GuruImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    private $importedCount = 0;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $this->importedCount++;

        return new Guru([
            'nama'               => $row['nama'],
            'nip'                => $row['nip'],
            'pengampu'           => $row['pengampu'],
            'email'              => $row['email'],
            'password'           => Hash::make($row['password'] ?? 'password'),
            'total_jam_mengajar' => $row['total_jam_mengajar'],
            'sisa_jam_mengajar'  => $row['total_jam_mengajar'], // Sisa jam di awal sama dengan total jam
            'profile_picture'    => 'Default-Profile.png',
            'tahun_ajaran_id'    => session('tahun_ajaran_id'),
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            // Validasi untuk setiap baris, menggunakan wildcard '*'
            'nama' => 'required|string|max:255',
            'nip' => [
                'required', 'integer',
                \Illuminate\Validation\Rule::unique('gurus')->where(fn ($query) => $query->where('tahun_ajaran_id', session('tahun_ajaran_id'))),
            ],
            'pengampu' => 'required|string|max:255',
            'email' => [
                'required', 'email',
                \Illuminate\Validation\Rule::unique('gurus')->where(fn ($query) => $query->where('tahun_ajaran_id', session('tahun_ajaran_id'))),
            ],
            'total_jam_mengajar' => 'required|integer|min:0',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nip.unique' => 'NIP :input pada file Excel sudah terdaftar di sistem.',
            'email.unique' => 'Email :input pada file Excel sudah terdaftar di sistem.',
        ];
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}