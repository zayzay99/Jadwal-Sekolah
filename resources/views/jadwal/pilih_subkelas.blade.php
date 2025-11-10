{{-- resources/views/jadwal/pilih_subkelas.blade.php --}}
@extends('dashboard.admin')
@section('content')

<div class="content-header">
    <h2 style="font-size: 1.8rem; font-weight: 700; color: var(--text-color); margin: 0;">
        <i class="fas fa-users-class" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-right: 10px;"></i>
        Pilih Kelas untuk Angkatan {{ $kategori }}
    </h2>
</div>

<!-- Welcome Card -->
<div class="welcome-card" style="margin-bottom: 25px;">
    <div class="welcome-text">
        <h2>Buat <strong>Jadwal Baru</strong></h2>
        <p>Pilih kelas dari angkatan {{ $kategori }} untuk membuat jadwal pembelajaran baru</p>
    </div>
    <div class="welcome-icon">
        <i class="fas fa-calendar-plus"></i>
    </div>
</div>

<!-- Main Table Card -->
<div style="background: white; border-radius: 20px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color); overflow: hidden;">
    
    <!-- Table Header -->
    <div style="padding: 25px 30px; border-bottom: 1px solid var(--border-color); background: linear-gradient(to right, rgba(17, 153, 142, 0.05), transparent);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
            <div>
                <h2 style="font-size: 1.3rem; font-weight: 700; color: var(--text-color); margin: 0 0 5px 0; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-list" style="color: var(--primary-color);"></i>
                    Daftar Kelas Angkatan {{ $kategori }}
                </h2>
                <p style="margin: 0; font-size: 0.9rem; color: var(--text-muted);">
                    <i class="fas fa-info-circle"></i>
                    Pilih kelas untuk mulai membuat jadwal
                </p>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('jadwal.pilihKelas') }}" 
                   class="btn btn-secondary" 
                   style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <button id="printSelectedBtn" class="btn btn-primary" style="display: none; align-items: center; gap: 8px;">
                    <i class="fas fa-print"></i>
                    <span>Cetak Jadwal Terpilih</span>
                </button>
            </div>
        </div>
        
        <!-- Search Bar -->
        <div style="position: relative; max-width: 500px;">
            <input type="text" 
                   id="searchKelas" 
                   class="form-control" 
                   placeholder="Cari nama kelas..."
                   style="padding-left: 45px; padding-right: 45px; height: 48px; font-size: 0.95rem; border-radius: 12px;">
            <i class="fas fa-search" style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1rem; pointer-events: none;"></i>
            <button id="clearSearch" 
                    style="display: none; position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: var(--text-muted); color: white; border: none; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; transition: var(--transition);"
                    title="Clear">
                <i class="fas fa-times" style="font-size: 0.85rem;"></i>
            </button>
        </div>
        
        <!-- Search Info -->
        <div id="searchInfo" style="display: none; margin-top: 12px; padding: 10px 15px; background: rgba(17, 153, 142, 0.1); border-radius: 8px; font-size: 0.9rem; color: var(--text-color);">
            <i class="fas fa-filter" style="color: var(--primary-color);"></i>
            Menampilkan <strong id="resultCount">0</strong> dari <strong id="totalCount">0</strong> kelas
        </div>
    </div>
    
    <!-- Table Content -->
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">
                        <input type="checkbox" id="selectAllClasses" class="form-check-input">
                    </th>
                    <th style="width: 55%;">Nama Kelas</th>
                    <th style="text-align: center; width: 40%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subkelas as $k)
                <tr>
                    <td style="text-align: center; vertical-align: middle;">
                        <input type="checkbox" name="selected_classes[]" value="{{ $k->id }}" class="class-checkbox form-check-input">
                    </td>
                    <td style="font-weight: 500; color: var(--text-color); vertical-align: middle;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 45px; height: 45px; border-radius: 12px; background: var(--primary-gradient); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-chalkboard" style="color: white; font-size: 1.1rem;"></i>
                            </div>
                            <div>
                                <div style="font-size: 1rem; font-weight: 600; color: var(--text-color);">
                                    {{ $k->nama_kelas }}
                                </div>
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 2px;">
                                    <i class="fas fa-layer-group" style="font-size: 0.75rem;"></i>
                                    Angkatan {{ $kategori }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center; padding: 16px 20px; vertical-align: middle;">
                        <a href="{{ route('jadwal.create', $k->id) }}" 
                           class="btn btn-primary" 
                           style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                            <i class="fas fa-plus"></i>
                            <span>Buat Jadwal</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
                        <i class="fas fa-inbox" style="font-size: 3.5rem; opacity: 0.3; display: block; margin-bottom: 20px;"></i>
                        <p style="margin: 0; font-size: 1.1rem; font-weight: 600;">Tidak Ada Kelas Tersedia</p>
                        <p style="margin: 8px 0 0 0; font-size: 0.9rem;">
                            Belum ada kelas untuk angkatan {{ $kategori }}
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Info Section -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 25px;">
    <!-- Stat Card 1 -->
    <div style="background: linear-gradient(135deg, rgba(17, 153, 142, 0.1), rgba(56, 239, 125, 0.1)); border-radius: 15px; padding: 25px; border-left: 4px solid var(--primary-color); transition: var(--transition);" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(17, 153, 142, 0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 60px; height: 60px; border-radius: 15px; background: var(--primary-gradient); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);">
                <i class="fas fa-users" style="color: white; font-size: 1.8rem;"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 2rem; font-weight: 700; color: var(--text-color);">
                    {{ count($subkelas) }}
                </h3>
                <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                    Total Kelas
                </p>
            </div>
        </div>
    </div>
    
    <!-- Stat Card 2 -->
    <div style="background: linear-gradient(135deg, rgba(79, 172, 254, 0.1), rgba(0, 180, 219, 0.1)); border-radius: 15px; padding: 25px; border-left: 4px solid #4facfe; transition: var(--transition);" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(79, 172, 254, 0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 60px; height: 60px; border-radius: 15px; background: var(--accent-gradient); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);">
                <i class="fas fa-graduation-cap" style="color: white; font-size: 1.8rem;"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--text-color);">
                    {{ $kategori }}
                </h3>
                <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                    Angkatan
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Tips Card -->
<div style="background: linear-gradient(135deg, rgba(242, 153, 74, 0.1), rgba(242, 201, 76, 0.1)); border-radius: 15px; padding: 20px 25px; margin-top: 25px; border-left: 4px solid #f2994a;">
    <div style="display: flex; align-items: flex-start; gap: 15px;">
        <i class="fas fa-lightbulb" style="font-size: 1.5rem; color: #f2994a; flex-shrink: 0;"></i>
        <div>
            <h4 style="margin: 0 0 8px 0; font-size: 1rem; font-weight: 600; color: var(--text-color);">Tips Membuat Jadwal</h4>
            <p style="margin: 0; font-size: 0.9rem; color: var(--text-light); line-height: 1.6;">
                Pastikan Anda sudah menyiapkan data mata pelajaran, guru, dan kategori jadwal sebelum membuat jadwal baru. Periksa juga konflik jadwal yang mungkin terjadi.
            </p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchKelas');
    const clearBtn = document.getElementById('clearSearch');
    const searchInfo = document.getElementById('searchInfo');
    const resultCount = document.getElementById('resultCount');
    const totalCount = document.getElementById('totalCount');
    const tableRows = document.querySelectorAll('.table tbody tr:not(:has(td[colspan]))');
    
    // Set total count
    totalCount.textContent = tableRows.length;
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;
        
        // Show/hide clear button
        clearBtn.style.display = searchTerm ? 'block' : 'none';
        
        // Filter rows
        tableRows.forEach(row => {
            const kelasName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            
            if (kelasName.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
                row.style.animation = 'fadeIn 0.3s ease';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update search info
        if (searchTerm) {
            searchInfo.style.display = 'block';
            resultCount.textContent = visibleCount;
            
            // Show no results message
            const emptyRow = document.querySelector('.table tbody tr[data-no-results]');
            if (visibleCount === 0) {
                if (!emptyRow) {
                    const noResultRow = document.createElement('tr');
                    noResultRow.setAttribute('data-no-results', 'true');
                    noResultRow.innerHTML = `
                        <td colspan="3" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                            <i class="fas fa-search" style="font-size: 2.5rem; opacity: 0.3; display: block; margin-bottom: 15px;"></i>
                            <p style="margin: 0; font-size: 1rem; font-weight: 600;">Tidak Ada Hasil</p>
                            <p style="margin: 5px 0 0 0; font-size: 0.85rem;">
                                Kelas dengan kata kunci "<strong>${searchTerm}</strong>" tidak ditemukan
                            </p>
                        </td>
                    `;
                    document.querySelector('.table tbody').appendChild(noResultRow);
                }
            } else {
                if (emptyRow) emptyRow.remove();
            }
        } else {
            searchInfo.style.display = 'none';
            const emptyRow = document.querySelector('.table tbody tr[data-no-results]');
            if (emptyRow) emptyRow.remove();
        }
    });
    
    // Clear search
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
    });

    // Bulk print functionality
    const selectAllClasses = document.getElementById('selectAllClasses');
    const classCheckboxes = document.querySelectorAll('.class-checkbox');
    const printSelectedBtn = document.getElementById('printSelectedBtn');

    function updatePrintButtonVisibility() {
        const anyChecked = Array.from(classCheckboxes).some(cb => cb.checked);
        printSelectedBtn.style.display = anyChecked ? 'flex' : 'none';
    }

    selectAllClasses.addEventListener('change', function() {
        const visibleCheckboxes = Array.from(classCheckboxes).filter(cb => {
            const row = cb.closest('tr');
            return row && row.style.display !== 'none';
        });
        
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updatePrintButtonVisibility();
    });

    classCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const visibleCheckboxes = Array.from(classCheckboxes).filter(cb => {
                const row = cb.closest('tr');
                return row && row.style.display !== 'none';
            });
            
            const allVisibleChecked = visibleCheckboxes.every(cb => cb.checked);
            selectAllClasses.checked = allVisibleChecked && visibleCheckboxes.length > 0;
            
            updatePrintButtonVisibility();
        });
    });

    printSelectedBtn.addEventListener('click', function() {
        const selectedClassIds = Array.from(classCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selectedClassIds.length > 0) {
            const url = "{{ route('admin.jadwal.cetak.bulk') }}?" + new URLSearchParams({
                kelas_ids: selectedClassIds.join(',')
            }).toString();
            window.open(url, '_blank');
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih setidaknya satu kelas untuk dicetak.',
                    confirmButtonColor: 'var(--primary-color)'
                });
            } else {
                alert('Pilih setidaknya satu kelas untuk dicetak.');
            }
        }
    });

    // Initial check for print button visibility
    updatePrintButtonVisibility();
    
    // Clear button hover effect
    clearBtn.addEventListener('mouseenter', function() {
        this.style.background = '#f5576c';
        this.style.transform = 'translateY(-50%) scale(1.1)';
    });
    
    clearBtn.addEventListener('mouseleave', function() {
        this.style.background = 'var(--text-muted)';
        this.style.transform = 'translateY(-50%) scale(1)';
    });
});
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Search input focus effect */
#searchKelas:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.1);
    transform: translateY(-2px);
}

#searchKelas:hover {
    border-color: var(--primary-color);
}

/* Responsive search */
@media (max-width: 768px) {
    #searchKelas {
        max-width: 100% !important;
    }
}
</style>
@endpush

@push('styles')
<style>
/* Table row hover effect */
.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    transform: scale(1.01);
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.1);
}

/* Button pulse animation */
@keyframes pulse {
    0%, 100% {
        box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
    }
    50% {
        box-shadow: 0 4px 25px rgba(17, 153, 142, 0.5);
    }
}

.btn-primary:hover {
    animation: pulse 1.5s infinite;
}

/* Responsive */
@media (max-width: 768px) {
    .content-header h2 {
        font-size: 1.4rem !important;
    }
    
    .welcome-card {
        flex-direction: column;
        text-align: center;
        padding: 25px 20px;
    }
    
    .table td:nth-child(2) > div {
        flex-direction: column;
        text-align: center;
    }
    
    .btn span {
        display: none;
    }
    
    .btn {
        padding: 10px 16px !important;
    }
}
</style>
@endpush