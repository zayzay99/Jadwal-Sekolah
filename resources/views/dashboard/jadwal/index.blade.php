{{-- Notifikasi Modal --}}
@if(session('success'))
<div id="notifModal" style="
    position: fixed; left: 0; top: 0; width: 100vw; height: 100vh; background: #0005; z-index: 9999; display: flex; align-items: center; justify-content: center;">
    <div style="background: #fff; padding: 32px 40px; border-radius: 12px; box-shadow: 0 4px 24px #0002; text-align: center;">
        <h3 style="margin-bottom: 16px; color: #2d6a4f;">Sukses!</h3>
        <div style="margin-bottom: 18px;">{{ session('success') }}</div>
        <button onclick="document.getElementById('notifModal').style.display='none'" style="padding: 8px 24px; background: #2d6a4f; color: #fff; border: none; border-radius: 6px; cursor: pointer;">Tutup</button>
    </div>
</div>
<script>
    // Tutup modal otomatis setelah 2.5 detik dan redirect
    setTimeout(function(){
        window.location.href = "{{ route('jadwal.pilihKelasLihat') }}";
    }, 2500);
</script>
@endif