@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2>Manajemen Slot Waktu</h2>
    <p>Atur semua slot waktu yang akan digunakan untuk menyusun jadwal pelajaran.</p>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="table-container">
    <div class="table-header">
        <h2>Daftar Slot Waktu</h2>
        <div>
            <a href="{{ route('manage.tabelj.create') }}" class="btn btn-success btn-tiny">
                <i class="fas fa-plus"></i> Tambah Slot Waktu
            </a>
            <a href="{{ route('manage.tabelj.assignCategory') }}" class="btn btn-info btn-tiny">
                <i class="fas fa-tags"></i> Tetapkan Kategori
            </a>
            <button id="clear-all-btn" class="btn btn-danger btn-tiny">
                <i class="fas fa-trash-alt"></i> Hapus Semua
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tabeljs as $tabelj)
                    <tr>
                        <td>{{ Carbon\Carbon::parse($tabelj->jam_mulai)->format('H:i') }}</td>
                        <td>{{ Carbon\Carbon::parse($tabelj->jam_selesai)->format('H:i') }}</td>
                        <td>{{ $tabelj->jadwalKategori->nama_kategori ?? 'Tidak ada kategori' }}</td>
                        <td>
                            <a href="{{ route('manage.tabelj.edit', $tabelj->id) }}" class="btn btn-warning btn-sm btn-tiny" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-info btn-sm add-break-btn btn-tiny" data-id="{{ $tabelj->id }}" title="Tambah Istirahat">
                                <i class="fas fa-clock"></i>
                            </button>
                            <form action="{{ route('manage.tabelj.destroy', $tabelj->id) }}" method="POST" style="display:inline;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-tiny" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada slot waktu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = '{{ csrf_token() }}';

    // Handle single delete
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda tidak akan dapat mengembalikan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Handle clear all
    const clearAllBtn = document.getElementById('clear-all-btn');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Hapus Semua Slot Waktu?',
                text: "Semua slot waktu akan dihapus secara permanen. Aksi ini tidak dapat dibatalkan.",
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form dynamically to send a DELETE request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("manage.tabelj.destroyAll") }}';
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);

                    

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    }

    // Handle Add Break
    const addBreakButtons = document.querySelectorAll('.add-break-btn');
    addBreakButtons.forEach(button => {
        button.addEventListener('click', function () {
            const tabeljId = this.dataset.id;
            
            Swal.fire({
                title: 'Tambah Jam Istirahat',
                input: 'number',
                inputLabel: 'Durasi Istirahat (menit)',
                inputPlaceholder: 'Contoh: 15',
                showCancelButton: true,
                confirmButtonText: 'Tambah',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value || value <= 0) {
                        return 'Durasi harus berupa angka positif!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const durasi = result.value;
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/manage/tabelj/${tabeljId}/add-break`;

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    const durasiInput = document.createElement('input');
                    durasiInput.type = 'hidden';
                    durasiInput.name = 'durasi_istirahat';
                    durasiInput.value = durasi;
                    form.appendChild(durasiInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem; /* Or any other spacing */
}
</style>
@endpush
