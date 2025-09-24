@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2>Import Data Siswa</h2>
    <p>Unggah file Excel atau CSV untuk menambahkan data siswa secara massal.</p>
</div>

<div class="form-container">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('import_errors'))
        <div class="alert alert-danger">
            <strong>Ditemukan beberapa error saat impor:</strong>
            <ul style="margin:0; padding-left:18px;">
                @foreach (session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('manage.siswa.import.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Pilih File (Excel/CSV)</label>
            <input type="file" id="file" name="file" required class="form-control-file">
            <small class="form-text">Pastikan file Anda memiliki kolom header: `Nama`, `NIS`, `Kelas`, dan `Email` (opsional).</small>
        </div>

        <div class="form-group">
            <p><strong>Catatan:</strong></p>
            <ul>
                <li>Kolom `Kelas` harus berisi nama kelas yang sudah terdaftar di sistem (contoh: X-A atau XII-RPL).</li>
                <li>Kolom `nis` dan `email` harus unik untuk setiap siswa.</li>
                <li>Password akan dibuat secara otomatis menggunakan `nis` sebagai password default.</li>
            </ul>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-file-import"></i> Import Data
            </button>
            <a href="{{ route('manage.siswa.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection