<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SiswaExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Mengambil semua data siswa beserta relasi kelasnya
        return Siswa::with('kelas')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Mendefinisikan header untuk kolom di file Excel
        return [
            'NIS',
            'Nama Lengkap',
            'Email',
            'Kelas',
        ];
    }

    /**
     * @param mixed $siswa
     * @return array
     */
    public function map($siswa): array
    {
        // Memetakan data setiap siswa ke dalam kolom yang sesuai
        return [
            $siswa->nis,
            $siswa->nama,
            $siswa->email,
            $siswa->kelas->first()->nama_kelas ?? 'Belum ada kelas', // Mengambil nama kelas pertama
        ];
    }
}
