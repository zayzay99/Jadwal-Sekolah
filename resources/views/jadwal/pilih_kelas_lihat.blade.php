@extends('dashboard.admin')

@section('content')
<div class="content-header">
    <h2 style="font-size: 1.8rem; font-weight: 700; color: var(--text-color); margin: 0;">
        <i class="fas fa-search" style="color: var(--primary-color); margin-right: 10px;"></i>
        Lihat Jadwal per Kelas
    </h2>
</div>

<div class="form-container" style="background: white; border-radius: 20px; padding: 40px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color);">

    <div style="background: var(--bg-primary); padding: 25px; border-radius: 15px; border-left: 4px solid var(--primary-color);">
        <h3 style="margin: 0 0 20px 0; font-size: 1.2rem; color: var(--text-color); display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-chalkboard-teacher" style="color: var(--primary-color);"></i>
            Pilih Kelas untuk Melihat Jadwal
        </h3>
        
        <div class="form-group">
            <label for="kelas_id" style="font-weight: 600; color: var(--text-color); margin-bottom: 8px; display: block;">
                <i class="fas fa-layer-group" style="color: var(--primary-color); margin-right: 5px;"></i>
                Cari dan Pilih Kelas
            </label>
            <select id="kelas_id" class="form-control select2-kelas">
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
            <small class="form-text text-muted" style="display: block; margin-top: 8px;">
                <i class="fas fa-info-circle"></i> Anda bisa mencari nama kelas dengan mengetik di kolom ini.
            </small>
        </div>
    </div>

    {{-- Info div for user feedback --}}
    <div id="jadwal-info" style="text-align: center; padding: 20px; background: var(--bg-primary); border-radius: 15px; display: none; margin-top: 20px;">
        <p><i class="fas fa-spinner fa-spin"></i> Mengarahkan ke halaman jadwal...</p>
    </div>

</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Select2 Styling */
    .select2-container--default .select2-selection--single {
        height: 48px;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        padding: 8px 15px;
        font-size: 0.95rem;
        transition: var(--transition);
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.1);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        color: var(--text-color);
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
    }
    .select2-dropdown {
        border: 2px solid var(--primary-color);
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(17, 153, 142, 0.15);
    }
    .select2-search--dropdown .select2-search__field {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.9rem;
    }
    .select2-search--dropdown .select2-search__field:focus {
        border-color: var(--primary-color);
        outline: none;
    }
    .select2-results__option {
        padding: 10px 15px;
        font-size: 0.95rem;
    }
    .select2-results__option--highlighted {
        background-color: var(--primary-color) !important;
        color: white;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 for Kelas dropdown
    $('.select2-kelas').select2({
        placeholder: "🔍 Cari dan pilih nama kelas...",
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Tidak ada kelas ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    });

    // Handle selection change for redirection
    $('#kelas_id').on('change', function() {
        var kelasId = $(this).val();
        if (kelasId) {
            // Show loading/redirecting info
            $('#jadwal-info').slideDown();

            // Define the URL template from the route.
            // This uses a placeholder ':id' which we'll replace.
            var urlTemplate = "{{ route('jadwal.perKelas', ['kelas' => ':id']) }}";
            
            // Replace the placeholder with the actual selected ID.
            var url = urlTemplate.replace(':id', kelasId);
            
            // Redirect to the schedule page.
            window.location.href = url;
        } else {
            // Hide the info box if no class is selected
            $('#jadwal-info').slideUp();
        }
    });
});
</script>
@endpush