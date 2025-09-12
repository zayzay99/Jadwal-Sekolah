@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2>Generator Slot Waktu Otomatis</h2>
    <p>Buat semua slot waktu pelajaran untuk satu hari dengan cepat.</p>
</div>

<div class="form-container" style="max-width: 800px; margin-bottom: 2rem;">
    <h3>Pengaturan Jadwal</h3>
    <form id="generate-slots-form">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label for="jam_masuk_sekolah">Jam Mulai Sekolah</label>
                <input type="time" id="jam_masuk_sekolah" name="jam_masuk_sekolah" class="form-control" value="07:00" required>
            </div>
            <div class="form-group">
                <label for="durasi_pelajaran">Durasi per Pelajaran (menit)</label>
                <input type="number" id="durasi_pelajaran" name="durasi_pelajaran" class="form-control" value="35" required>
            </div>
            <div class="form-group">
                <label for="jumlah_pelajaran">Jumlah Pelajaran per Hari</label>
                <input type="number" id="jumlah_pelajaran" name="jumlah_pelajaran" class="form-control" value="10" required>
            </div>
            <div class="form-group">
                <label for="durasi_istirahat">Durasi Istirahat (menit)</label>
                <input type="number" id="durasi_istirahat" name="durasi_istirahat" class="form-control" value="15">
            </div>
            <div class="form-group">
                <label for="istirahat_setelah_jam_ke">Istirahat Setelah Pelajaran ke-</label>
                <input type="number" id="istirahat_setelah_jam_ke" name="istirahat_setelah_jam_ke" class="form-control" value="4">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-cogs"></i> Buat Jadwal
            </button>
        </div>
    </form>
</div>

<div class="table-container">
    <div class="table-header">
        <h2>Daftar Slot Waktu</h2>
        <button id="clear-all-btn" class="btn btn-danger">Hapus Semua</button>
    </div>
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="timeslots-table-body">
                @forelse ($tabeljs as $tabelj)
                    <tr id="timeslot-{{ $tabelj->id }}">
                        <td>{{ $tabelj->jam_mulai }}</td>
                        <td>{{ $tabelj->jam_selesai }}</td>
                        <td>
                            <button class="btn btn-danger delete-timeslot-btn" data-id="{{ $tabelj->id }}">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr id="no-data-row">
                        <td colspan="3" class="text-center">Belum ada slot waktu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = '{{ csrf_token() }}';
    const form = document.getElementById('generate-slots-form');
    const tableBody = document.getElementById('timeslots-table-body');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        const confirmation = await Swal.fire({
            title: 'Buat Jadwal Baru?',
            text: "Ini akan menghapus semua slot waktu yang ada dan menggantinya dengan yang baru. Lanjutkan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, buat!',
            cancelButtonText: 'Batal'
        });

        if (!confirmation.isConfirmed) return;

        try {
            const response = await fetch('{{ route("tabelj.generate") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                tableBody.innerHTML = ''; // Clear existing table
                result.timeSlots.forEach(slot => {
                    const newRow = `
                        <tr id="timeslot-${slot.id}">
                            <td>${slot.jam_mulai}</td>
                            <td>${slot.jam_selesai}</td>
                            <td>
                                <a href="/tabelj/${slot.id}/edit" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn btn-danger btn-sm delete-timeslot-btn" data-id="${slot.id}">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', newRow);
                });
                Swal.fire('Berhasil!', result.message, 'success');
            } else {
                let errorMessages = '';
                if (result.errors) {
                    for (const key in result.errors) {
                        errorMessages += `${result.errors[key].join(', ')}<br>`
                    }
                }
                Swal.fire('Gagal!', result.message || errorMessages, 'error');
            }
        } catch (error) {
            Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
        }
    });

    // --- Delete and Clear All Logic ---
    async function deleteSlot(timeslotId) {
        const confirmation = await Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Slot waktu ini akan dihapus secara permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal'
        });

        if (!confirmation.isConfirmed) return;

        try {
            const response = await fetch(`/tabelj/${timeslotId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();

            if (response.ok && result.success) {
                document.getElementById(`timeslot-${timeslotId}`).remove();
                if (tableBody.children.length === 0) {
                    tableBody.innerHTML = '<tr id="no-data-row"><td colspan="3" class="text-center">Belum ada slot waktu.</td></tr>';
                }
                Swal.fire('Dihapus!', result.message, 'success');
            } else {
                Swal.fire('Gagal!', result.message || 'Gagal menghapus slot.', 'error');
            }
        } catch (error) {
            Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
        }
    }

    tableBody.addEventListener('click', function (e) {
        if (e.target.closest('.delete-timeslot-btn')) {
            const button = e.target.closest('.delete-timeslot-btn');
            deleteSlot(button.dataset.id);
        }
    });

    document.getElementById('clear-all-btn').addEventListener('click', async function() {
        const confirmation = await Swal.fire({
            title: 'Hapus Semua Slot Waktu?',
            text: "Semua slot waktu akan dihapus. Aksi ini tidak dapat dibatalkan.",
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus Semua',
            cancelButtonText: 'Batal'
        });

        if (!confirmation.isConfirmed) return;

        try {
            const response = await fetch('{{ route("tabelj.clear") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();

            if (response.ok && result.success) {
                tableBody.innerHTML = '<tr id="no-data-row"><td colspan="3" class="text-center">Belum ada slot waktu.</td></tr>';
                Swal.fire('Berhasil!', result.message, 'success');
            } else {
                Swal.fire('Gagal!', result.message || 'Gagal menghapus semua slot.', 'error');
            }
        } catch (error) {
            Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
        }
    });
});
</script>
@endpush