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
        Schema::table('kelas_siswa', function (Blueprint $table) {
            $table->unsignedBigInteger('tahun_ajaran_id')->nullable()->after('kelas_id');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->onDelete('cascade');

            // Make sure a student can only be in one class per school year
            $table->unique(['siswa_id', 'tahun_ajaran_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas_siswa', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropUnique(['siswa_id', 'tahun_ajaran_id']);
            $table->dropColumn('tahun_ajaran_id');
        });
    }
};
