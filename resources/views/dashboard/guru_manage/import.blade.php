@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2>Import Guru</h2>
</div>

<div class="card-form">
    <div class="card-header">
        <h3>Formulir Import Data Guru</h3>
        <p>Unggah file Excel (.xlsx, .xls) untuk menambahkan beberapa guru sekaligus.</p>
    </div>
    <div class="card-body">
        <div class="import-instructions">
            <h4>Petunjuk:</h4>
            <ol>
                <li>Unduh template file Excel yang sudah disediakan.</li>
                <li>Isi data guru sesuai dengan kolom yang ada. Kolom <strong>NIP</strong> dan <strong>Email</strong> harus unik.</li>
                <li>Kolom <strong>Password</strong> akan diisi otomatis jika dikosongkan (default: `password`).</li>
                <li>Kolom <strong>Total Jam Mengajar</strong> adalah jumlah jam wajib mengajar per minggu.</li>
                <li>Pastikan format file adalah <strong>.xlsx</strong> atau <strong>.xls</strong>.</li>
            </ol>
            <a href="{{ asset('templates/template_guru.xlsx') }}" class="btn btn-secondary" download>
                <i class="fas fa-download"></i> Unduh Template
            </a>
        </div>

        <hr>

        <form action="{{ route('manage.guru.import.store') }}" method="POST" enctype="multipart/form-data" class="styled-form">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group">
                <label for="file">Pilih File Excel</label>
                <input type="file" name="file" id="file" class="form-control-file" required accept=".xlsx, .xls">
                <small class="form-text text-muted">Hanya file dengan format .xlsx atau .xls yang diterima.</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Unggah dan Import
                </button>
                <a href="{{ route('manage.guru.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection