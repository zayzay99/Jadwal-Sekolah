<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            // Cek apakah indeks global masih ada sebelum mencoba menghapusnya.
            // Ini membuat migrasi lebih aman jika dijalankan ulang atau jika skema berubah.
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('gurus');

            if (isset($indexes['gurus_nip_unique'])) {
                $table->dropUnique('gurus_nip_unique');
            }
            if (isset($indexes['gurus_email_unique'])) {
                $table->dropUnique('gurus_email_unique');
            }

            // Tambahkan indeks unik komposit pada 'nip' dan 'tahun_ajaran_id'
            // Memberi nama eksplisit pada indeks membuatnya lebih mudah dikelola.
            $table->unique(['nip', 'tahun_ajaran_id'], 'gurus_nip_tahun_ajaran_id_unique');
            // Tambahkan indeks unik komposit pada 'email' dan 'tahun_ajaran_id'
            $table->unique(['email', 'tahun_ajaran_id'], 'gurus_email_tahun_ajaran_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            // Hapus indeks unik komposit
            $table->dropUnique('gurus_nip_tahun_ajaran_id_unique');
            $table->dropUnique('gurus_email_tahun_ajaran_id_unique');

            // Tambahkan kembali indeks unik global jika memang itu yang diinginkan sebelumnya
            // (Namun, untuk konsistensi dengan validasi aplikasi, indeks komposit lebih tepat)
            $table->unique('nip', 'gurus_nip_unique');
            $table->unique('email', 'gurus_email_unique');
        });
    }
};
