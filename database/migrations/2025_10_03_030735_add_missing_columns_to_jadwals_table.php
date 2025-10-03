<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            // Cek apakah kolom sudah ada sebelum menambahkan
            if (!Schema::hasColumn('jadwals', 'kelas_id')) {
                $table->unsignedBigInteger('kelas_id')->after('guru_id');
                $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('jadwals', 'tabelj_id')) {
                $table->unsignedBigInteger('tabelj_id')->nullable()->after('jam');
                $table->foreign('tabelj_id')->references('id')->on('tabeljs')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('jadwals', 'jadwal_kategori_id')) {
                $table->unsignedBigInteger('jadwal_kategori_id')->nullable()->after('tabelj_id');
                $table->foreign('jadwal_kategori_id')->references('id')->on('jadwal_kategoris')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('jadwals', 'tahun_ajaran_id')) {
                $table->unsignedBigInteger('tahun_ajaran_id')->after('jadwal_kategori_id');
                $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            if (Schema::hasColumn('jadwals', 'kelas_id')) {
                $table->dropForeign(['kelas_id']);
                $table->dropColumn('kelas_id');
            }
            
            if (Schema::hasColumn('jadwals', 'tabelj_id')) {
                $table->dropForeign(['tabelj_id']);
                $table->dropColumn('tabelj_id');
            }
            
            if (Schema::hasColumn('jadwals', 'jadwal_kategori_id')) {
                $table->dropForeign(['jadwal_kategori_id']);
                $table->dropColumn('jadwal_kategori_id');
            }
            
            if (Schema::hasColumn('jadwals', 'tahun_ajaran_id')) {
                $table->dropForeign(['tahun_ajaran_id']);
                $table->dropColumn('tahun_ajaran_id');
            }
        });
    }
};