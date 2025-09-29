@extends('layouts.app') {{-- Assuming a layout file --}}

@section('content')
<div class="container">
    <h2>Buat Tahun Ajaran Baru</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tahun-ajaran.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <h4>Detail Tahun Ajaran</h4>
                <div class="mb-3">
                    <label for="tahun_ajaran" class="form-label">Tahun Ajaran (format: YYYY/YYYY)</label>
                    <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" placeholder="Contoh: 2025/2026" required>
                </div>
                <div class="mb-3">
                    <label for="semester" class="form-label">Semester</label>
                    <select class="form-select" id="semester" name="semester" required>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1">
                    <label class="form-check-label" for="is_active">
                        Langsung aktifkan tahun ajaran ini
                    </label>
                </div>

                <hr>

                <h4>Opsi Kloning Data</h4>
                <p class="text-muted">Pilih tahun ajaran untuk menyalin (kloning) data yang sudah ada. Jika tidak dipilih, tahun ajaran baru akan kosong.</p>

                <div class="mb-3">
                    <label for="source_tahun_ajaran_id" class="form-label">Salin Data dari Tahun Ajaran:</label>
                    <select class="form-select" id="source_tahun_ajaran_id" name="source_tahun_ajaran_id">
                        <option value="">-- Jangan Salin Data (Buat Kosong) --</option>
                        @foreach($tahunAjarans as $tahun)
                            <option value="{{ $tahun->id }}">{{ $tahun->tahun_ajaran }} - {{ $tahun->semester }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <p>Opsi untuk data yang disalin:</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="skip_kelas_assignments" name="skip_kelas_assignments" value="1">
                        <label class="form-check-label" for="skip_kelas_assignments">
                            <strong>Kosongkan Penempatan Kelas</strong> (Struktur kelas disalin, tapi tanpa siswa di dalamnya)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="skip_jadwal" name="skip_jadwal" value="1">
                        <label class="form-check-label" for="skip_jadwal">
                            <strong>Kosongkan Jadwal Pelajaran</strong> (Tidak ada jadwal yang disalin)
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('tahun-ajaran.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection
