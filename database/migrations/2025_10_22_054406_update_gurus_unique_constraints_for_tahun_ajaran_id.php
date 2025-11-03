<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ambil daftar index yang ada
        $indexes = DB::select("SHOW INDEXES FROM gurus");
        $indexNames = collect($indexes)->pluck('Key_name')->toArray();

        Schema::table('gurus', function (Blueprint $table) use ($indexNames) {
            // Drop index lama jika ada
            if (in_array('gurus_nip_unique', $indexNames)) {
                $table->dropUnique('gurus_nip_unique');
            }
            if (in_array('gurus_email_unique', $indexNames)) {
                $table->dropUnique('gurus_email_unique');
            }
        });

        // Buat index baru di Schema terpisah
        Schema::table('gurus', function (Blueprint $table) use ($indexNames) {
            // Tambahkan index baru hanya jika belum ada
            if (!in_array('gurus_nip_tahun_ajaran_id_unique', $indexNames)) {
                $table->unique(['nip', 'tahun_ajaran_id'], 'gurus_nip_tahun_ajaran_id_unique');
            }
            
            if (!in_array('gurus_email_tahun_ajaran_id_unique', $indexNames)) {
                $table->unique(['email', 'tahun_ajaran_id'], 'gurus_email_tahun_ajaran_id_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ambil daftar index yang ada
        $indexes = DB::select("SHOW INDEXES FROM gurus");
        $indexNames = collect($indexes)->pluck('Key_name')->toArray();

        Schema::table('gurus', function (Blueprint $table) use ($indexNames) {
            // Hapus indeks unik komposit jika ada
            if (in_array('gurus_nip_tahun_ajaran_id_unique', $indexNames)) {
                $table->dropUnique('gurus_nip_tahun_ajaran_id_unique');
            }
            
            if (in_array('gurus_email_tahun_ajaran_id_unique', $indexNames)) {
                $table->dropUnique('gurus_email_tahun_ajaran_id_unique');
            }
        });

        // Buat kembali index lama di Schema terpisah
        Schema::table('gurus', function (Blueprint $table) use ($indexNames) {
            // Tambahkan kembali indeks unik global jika belum ada
            if (!in_array('gurus_nip_unique', $indexNames)) {
                $table->unique('nip', 'gurus_nip_unique');
            }
            
            if (!in_array('gurus_email_unique', $indexNames)) {
                $table->unique('email', 'gurus_email_unique');
            }
        });
    }
};