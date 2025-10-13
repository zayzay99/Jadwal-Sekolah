@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 700; margin: 0 0 8px 0; color: var(--text-color);">
            Generate Slot Waktu
        </h2>
        <p style="color: var(--text-light); margin: 0; font-size: 0.95rem;">
            Buat beberapa slot waktu sekaligus dengan menentukan waktu mulai, jumlah jam pelajaran, durasi per jam, dan waktu istirahat.
        </p>
    </div>
</div>

<div style="background: white; padding: 35px 40px; border-radius: 20px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color); margin-top: 25px;">
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
            <label for="jam_mulai">
                <i class="fas fa-clock" style="margin-right: 8px; color: var(--primary-color);"></i>
                Mulai dari Jam
            </label>
            <input type="time" name="jam_mulai" id="jam_mulai" required class="form-control" value="{{ old('jam_mulai', '07:00') }}">
        </div>

        <div class="form-group">
            <label for="jumlah_jam_pelajaran">
                <i class="fas fa-list-ol" style="margin-right: 8px; color: var(--primary-color);"></i>
                Jumlah Jam Pelajaran
            </label>
            <input type="number" name="jumlah_jam_pelajaran" id="jumlah_jam_pelajaran" required class="form-control" value="{{ old('jumlah_jam_pelajaran', '8') }}" min="1" placeholder="Contoh: 8">
        </div>

        <div class="form-group">
            <label for="durasi">
                <i class="fas fa-hourglass-half" style="margin-right: 8px; color: var(--primary-color);"></i>
                Durasi per Jam Pelajaran (menit)
            </label>
            <input type="number" name="durasi" id="durasi" required class="form-control" value="{{ old('durasi', '45') }}" min="1" placeholder="Contoh: 45">
        </div>

        <div style="margin: 25px 0; padding: 20px; background: var(--bg-primary); border-radius: 15px; border: 2px dashed var(--border-color);">
            <label style="font-weight: 600; color: var(--text-color); margin-bottom: 15px; display: block;">
                <i class="fas fa-coffee" style="margin-right: 8px; color: var(--primary-color);"></i>
                Waktu Istirahat
            </label>
            
            <div id="istirahat-container">
                <!-- Break time fields will be added here dynamically -->
            </div>

            <button type="button" id="add-istirahat" class="btn btn-info" style="margin-top: 15px; width: 100%;">
                <i class="fas fa-plus"></i> Tambah Waktu Istirahat
            </button>
        </div>

        <div class="form-group">
            <div class="form-check" style="padding: 18px; background: rgba(245, 87, 108, 0.05); border-radius: 12px; border: 2px solid rgba(245, 87, 108, 0.2);">
                <input class="form-check-input" type="checkbox" name="replace_existing" id="replace_existing" value="1" checked style="margin-top: 2px;">
                <label class="form-check-label" for="replace_existing" style="cursor: pointer; font-weight: 500; color: var(--text-color);">
                    <i class="fas fa-exclamation-triangle" style="color: #f5576c; margin-right: 8px;"></i>
                    Hapus slot waktu yang ada sebelum generate
                    <small style="display: block; margin-top: 5px; color: var(--text-muted); font-weight: 400;">
                        Semua slot waktu yang ada akan dihapus dan diganti dengan yang baru
                    </small>
                </label>
            </div>
        </div>
        
        <div class="form-actions" style="margin-top: 30px; padding-top: 25px; border-top: 1px solid var(--border-color);">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-cogs"></i> Generate Slot Waktu
            </button>
            <a href="{{ route('manage.tabelj.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
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
        div.classList.add('istirahat-item');
        div.style.cssText = 'margin-bottom: 15px; padding: 20px; background: white; border-radius: 12px; border: 2px solid var(--border-color); transition: var(--transition);';
        div.innerHTML = `
            <label style="font-weight: 600; color: var(--text-color); margin-bottom: 12px; display: block;">
                <i class="fas fa-mug-hot" style="color: var(--primary-color); margin-right: 8px;"></i>
                Istirahat #${istirahatCount}
            </label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <input type="number" name="istirahat_setelah_jam_ke[]" class="form-control" placeholder="Setelah jam ke..." min="1" required style="padding: 12px;">
                    <small style="color: var(--text-muted); font-size: 0.8rem; margin-top: 5px; display: block;">Contoh: 2 (setelah jam ke-2)</small>
                </div>
                <div>
                    <input type="number" name="durasi_istirahat_menit[]" class="form-control" placeholder="Durasi (menit)" min="1" required style="padding: 12px;">
                    <small style="color: var(--text-muted); font-size: 0.8rem; margin-top: 5px; display: block;">Contoh: 15</small>
                </div>
            </div>
            <input type="text" name="keterangan_istirahat[]" class="form-control" placeholder="Keterangan (misal: Istirahat Pagi)" style="margin-bottom: 12px;">
            <button type="button" class="btn btn-danger btn-sm remove-istirahat" style="width: 100%;">
                <i class="fas fa-trash"></i> Hapus Istirahat Ini
            </button>
        `;
        container.appendChild(div);
        
        // Add hover effect
        div.addEventListener('mouseenter', function() {
            this.style.borderColor = 'var(--primary-color)';
            this.style.boxShadow = '0 4px 15px rgba(17, 153, 142, 0.15)';
        });
        div.addEventListener('mouseleave', function() {
            this.style.borderColor = 'var(--border-color)';
            this.style.boxShadow = 'none';
        });
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-istirahat')) {
            e.target.closest('.istirahat-item').remove();
            // Re-number labels
            const labels = container.querySelectorAll('.istirahat-item label');
            labels.forEach((label, index) => {
                label.innerHTML = `<i class="fas fa-mug-hot" style="color: var(--primary-color); margin-right: 8px;"></i>Istirahat #${index + 1}`;
            });
            istirahatCount = labels.length;
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.1);
}

.istirahat-item {
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush