@extends('dashboard.admin') 
@section('content')  
<div class="content-header"> 
    <h1>Lihat Jadwal Kelas {{ $kelas->nama_kelas }}</h1>          
    <p>Daftar jadwal untuk kelas {{ $kelas->nama_kelas }}</p> 
</div>  

<!-- PENTING: Tambahkan class jadwal-table-container di sini -->
<div class="table-container jadwal-table-container">     
    <div class="table-header">          
        <button class="btn btn-secondary btn-tiny" onclick="window.history.back()">             
            <i class="fas fa-arrow-left"></i> Kembali         
        </button>
        <a href="{{ route('admin.jadwal.cetak', ['kelas' => $kelas->id]) }}" class="btn btn-primary btn-tiny">             
            <i class="fas fa-print"></i> Cetak PDF         
        </a>
        @if($is_management && $jadwals->count() > 0)
        <form id="delete-all-form" action="{{ route('jadwal.destroyAll', $kelas->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-danger" onclick="showDeleteAllConfirmation(event)"><i class="fas fa-trash-alt"></i> Hapus Semua</button>
        </form>
        @endif
    </div>      

    <div class="table-responsive">         
        <table class="custom-table w-full">             
            <thead>                 
                <tr>                     
                    <th>Hari</th>                     
                    <th>Jam</th>                     
                    <th>Mata Pelajaran</th>                     
                    <th>Guru</th>                 
                </tr>             
            </thead>             
            <tbody>                 
                @if($jadwals->count() > 0)                     
                    @foreach($jadwals as $hari => $jadwalHarian)                         
                        @foreach($jadwalHarian as $index => $jadwal)                             
                            <tr>                                 
                                @if($index === 0)                                     
                                    <td rowspan="{{ count($jadwalHarian) }}">{{ $hari }}</td>                                 
                                @endif                                 
                                <td>{{ $jadwal->jam }}</td>                                 
                                @if($jadwal->kategori)                                     
                                    <td colspan="2" style="text-align: center; font-weight: bold;">{{ $jadwal->kategori->nama_kategori }}</td>                                 
                                @else                                     
                                    <td>{{ $jadwal->mapel }}</td>                                     
                                    <td>{{ $jadwal->guru ? $jadwal->guru->nama : '-' }}</td>                                 
                                @endif                                 
                                                             
                            </tr>                         
                        @endforeach                     
                    @endforeach                 
                @else                     
                    <tr>                         
                        <td colspan="4" class="no-data">                             
                            <i class="fas fa-info-circle"></i> Tidak ada jadwal untuk kelas ini                         
                        </td>                     
                    </tr>                 
                @endif             
            </tbody>         
        </table>      
    </div> 
</div>  

<script>     
    @if (session('success'))         
        Swal.fire({             
            position: "top-end",             
            icon: "success",             
            title: "Your work has been saved",             
            showConfirmButton: false,             
            timer: 1500         
        });     
    @endif 
</script>  



@if($is_management)
<script>
function showDeleteAllConfirmation(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Semua jadwal untuk kelas ini akan dihapus secara permanen! Tindakan ini tidak dapat dibatalkan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-all-form').submit();
        }
    });
}
</script>
@endif

@endsection