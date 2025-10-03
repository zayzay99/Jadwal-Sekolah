@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Edit Tahun Ajaran</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('manage.tahun-ajaran.update', $tahunAjaran) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="tahun_ajaran" class="form-label">Tahun Ajaran (format: YYYY/YYYY)</label>
            <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaran->tahun_ajaran) }}" required pattern="^\d{4}\/\d{4}$">
        </div>
        <div class="mb-3">
            <label for="semester" class="form-label">Semester</label>
            <input type="text" class="form-control" id="semester" name="semester" value="{{ old('semester', $tahunAjaran->semester) }}" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $tahunAjaran->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Jadikan tahun ajaran ini aktif</label>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('manage.tahun-ajaran.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
