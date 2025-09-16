@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2>Generate Slot Waktu</h2>
    <p>Buat beberapa slot waktu sekaligus dengan menentukan waktu mulai, jumlah jam pelajaran, durasi per jam, dan waktu istirahat.</p>
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

    <form action="{{ route('manage.tabelj.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="jam_mulai">Mulai dari Jam</label>
            <input type="time" name="jam_mulai" id="jam_mulai" required class="form-control" value="{{ old('jam_mulai', '07:00') }}">
        </div>
        <div class="form-group">
            <label for="jumlah_jam_pelajaran">Jumlah Jam Pelajaran</label>
            <input type="number" name="jumlah_jam_pelajaran" id="jumlah_jam_pelajaran" required class="form-control" value="{{ old('jumlah_jam_pelajaran', '8') }}" min="1">
        </div>
        <div class="form-group">
            <label for="durasi">Durasi per Jam Pelajaran (menit)</label>
            <input type="number" name="durasi" id="durasi" required class="form-control" value="{{ old('durasi', '45') }}" min="1">
        </div>
        <div id="istirahat-container">
            <!-- Break time fields will be added here dynamically -->
        </div>

        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="replace_existing" id="replace_existing" value="1" checked>
                <label class="form-check-label" for="replace_existing">
                    Hapus slot waktu yang ada sebelum generate
                </label>
            </div>
        </div>

        <div class="form-group">
            <button type="button" id="add-istirahat" class="btn btn-info"><i class="fas fa-plus"></i> Tambah Istirahat</button>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-cogs"></i> Generate
            </button>
            <a href="{{ route('manage.tabelj.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('istirahat-container');
    const addButton = document.getElementById('add-istirahat');
    let istirahatCount = 0;

    addButton.addEventListener('click', function () {
        istirahatCount++;
        const div = document.createElement('div');
        div.classList.add('form-group', 'istirahat-item');
        div.innerHTML = `
            <label>Istirahat #${istirahatCount}</label>
            <div class="input-group">
                <input type="number" name="istirahat_setelah_jam_ke[]" class="form-control" placeholder="Istirahat setelah Jam ke..." min="1" required>
                <input type="number" name="durasi_istirahat_menit[]" class="form-control" placeholder="Durasi Istirahat (menit)" min="1" required>
                <input type="text" name="keterangan_istirahat[]" class="form-control" placeholder="Keterangan (misal: Istirahat Pagi)">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-istirahat"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
        container.appendChild(div);
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-istirahat')) {
            e.target.closest('.istirahat-item').remove();
            // Re-number labels if needed
            const labels = container.querySelectorAll('.istirahat-item label');
            labels.forEach((label, index) => {
                label.textContent = `Istirahat #${index + 1}`;
            });
            istirahatCount = labels.length;
        }
    });
});
</script>
@endpush
