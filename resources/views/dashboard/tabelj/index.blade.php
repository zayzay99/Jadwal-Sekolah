@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2 style="margin: 0; font-size: 1.8rem; font-weight: 700; color: var(--text-color);">Manajemen Slot Waktu</h2>
</div>

<!-- Alerts -->
@if (session('success'))
    <div class="alert alert-success" style="margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger" style="margin-bottom: 20px;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<!-- Welcome Card -->
<div class="welcome-card" style="margin-bottom: 25px;">
    <div class="welcome-text" style="flex: 1;">
        <h2 style="font-size: 1.5rem; margin-bottom: 15px;">
            Kelola <strong>Slot Waktu</strong>
        </h2>
        <p style="color: var(--text-light); font-size: 0.95rem; margin: 0;">
            Atur semua slot waktu yang akan digunakan untuk menyusun jadwal pelajaran
        </p>
    </div>
    <div class="welcome-icon">
        <i class="fas fa-clock"></i>
    </div>
</div>

<!-- Table Container -->
<div class="table-container" style="background: white; border-radius: 20px; padding: 30px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color);">
    <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <h2 style="margin: 0; font-size: 1.3rem; font-weight: 700; color: var(--text-color);">
            <i class="fas fa-list-ul" style="margin-right: 10px; color: var(--primary-color);"></i>
            Daftar Slot Waktu
            <span class="badge" style="background: var(--accent-gradient); color: white; margin-left: 10px; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem;">
                {{ $tabeljs->count() }} Slot
            </span>
        </h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('manage.tabelj.create') }}" class="btn btn-success" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                <i class="fas fa-plus"></i> Generate Slot
            </a>
            <a href="{{ route('manage.tabelj.assignCategory') }}" class="btn btn-info" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                <i class="fas fa-tags"></i> Tetapkan Kategori
            </a>
            <button id="clear-all-btn" class="btn btn-danger" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-trash-alt"></i> Hapus Semua
            </button>
        </div>
    </div>
    
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 15px; overflow: hidden;">
            <thead style="background: var(--primary-gradient);">
                <tr>
                    <th style="padding: 15px 20px; text-align: center; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">No</th>
                    <th style="padding: 15px 20px; text-align: center; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Jam Mulai</th>
                    <th style="padding: 15px 20px; text-align: center; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Jam Selesai</th>
                    <th style="padding: 15px 20px; text-align: center; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Kategori</th>
                    <th style="padding: 15px 20px; text-align: center; color: white; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tabeljs as $index => $tabelj)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: var(--transition);">
                        <td style="padding: 18px 20px; text-align: center; font-weight: 600; color: var(--text-color);">
                            {{ $index + 1 }}
                        </td>
                        <td style="padding: 18px 20px; text-align: center; color: var(--text-color);">
                            <i class="fas fa-clock" style="margin-right: 8px; color: var(--primary-color);"></i>
                            <strong>{{ Carbon\Carbon::parse($tabelj->jam_mulai)->format('H:i') }}</strong>
                        </td>
                        <td style="padding: 18px 20px; text-align: center; color: var(--text-color);">
                            <i class="fas fa-clock" style="margin-right: 8px; color: var(--primary-color);"></i>
                            <strong>{{ Carbon\Carbon::parse($tabelj->jam_selesai)->format('H:i') }}</strong>
                        </td>
                        <td style="padding: 18px 20px; text-align: center;">
                            @if($tabelj->jadwalKategori)
                                <span class="badge" style="background: var(--success-gradient); color: white; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                    <i class="fas fa-tag" style="margin-right: 5px;"></i>
                                    {{ $tabelj->jadwalKategori->nama_kategori }}
                                </span>
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">
                                    <i class="fas fa-minus-circle" style="margin-right: 5px;"></i>
                                    Tidak ada kategori
                                </span>
                            @endif
                        </td>
                        <td style="padding: 18px 20px; text-align: center;">
                            <div class="action-buttons" style="display: flex; gap: 8px; justify-content: center; align-items: center; flex-wrap: wrap;">
                                <a href="{{ route('manage.tabelj.edit', $tabelj->id) }}" class="btn btn-info btn-sm" title="Edit" style="display: inline-flex; align-items: center; gap: 5px; text-decoration: none; padding: 8px 16px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn btn-warning btn-sm add-break-btn" data-id="{{ $tabelj->id }}" title="Tambah Istirahat" style="display: inline-flex; align-items: center; gap: 5px; padding: 8px 16px;">
                                    <i class="fas fa-coffee"></i> Istirahat
                                </button>
                                <form action="{{ route('manage.tabelj.destroy', $tabelj->id) }}" method="POST" style="display:inline; margin: 0;" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus" style="display: inline-flex; align-items: center; gap: 5px; padding: 8px 16px;">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 50px 20px; text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                                <i class="fas fa-clock" style="font-size: 3rem; color: var(--text-muted); opacity: 0.5;"></i>
                                <p style="margin: 0; color: var(--text-muted); font-size: 1rem; font-weight: 500;">
                                    Belum ada slot waktu yang tersedia
                                </p>
                                <a href="{{ route('manage.tabelj.create') }}" class="btn btn-primary btn-sm" style="text-decoration: none; margin-top: 10px;">
                                    <i class="fas fa-plus"></i> Generate Slot Waktu Baru
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    /* Table Hover Effect */
    .table tbody tr:hover {
        background-color: rgba(17, 153, 142, 0.05);
        transform: scale(1.01);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }

    /* Warning button styling */
    .btn-warning {
        background: var(--warning-gradient);
        color: white;
        box-shadow: 0 4px 15px rgba(242, 153, 74, 0.3);
    }

    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(242, 153, 74, 0.4);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .table-header {
            flex-direction: column;
            align-items: stretch !important;
        }
        
        .table-header > div {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .table-header .btn {
            width: 100%;
            justify-content: center;
        }
        
        .table {
            font-size: 0.85rem;
        }
        
        .table th,
        .table td {
            padding: 12px 10px;
        }
        
        .action-buttons {
            flex-direction: column;
            width: 100%;
        }
        
        .action-buttons .btn,
        .action-buttons form {
            width: 100%;
        }
        
        .action-buttons .btn {
            justify-content: center;
        }
    }
</style>
@endpush

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
                text: "Slot waktu ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f5576c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
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
                text: "Semua slot waktu akan dihapus secara permanen. Aksi ini tidak dapat dibatalkan!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#f5576c',
                cancelButtonColor: '#6c757d',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
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
                title: '☕ Tambah Jam Istirahat',
                html: `
                    <div style="text-align: left; margin-top: 20px;">
                        <label style="font-weight: 600; color: var(--text-color); margin-bottom: 8px; display: block;">
                            <i class="fas fa-clock" style="color: var(--primary-color);"></i> Durasi Istirahat (menit)
                        </label>
                        <input id="durasi-istirahat" type="number" class="swal2-input" placeholder="Contoh: 15" min="1" style="width: 90%; padding: 12px; border: 2px solid var(--border-color); border-radius: 10px;">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check"></i> Tambah Istirahat',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                confirmButtonColor: 'var(--primary-color)',
                cancelButtonColor: '#6c757d',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false,
                preConfirm: () => {
                    const durasi = document.getElementById('durasi-istirahat').value;
                    if (!durasi || durasi <= 0) {
                        Swal.showValidationMessage('Durasi harus berupa angka positif!');
                        return false;
                    }
                    return durasi;
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