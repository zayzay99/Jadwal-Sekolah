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
        Schema::table('kelas', function (Blueprint $table) {
            if (Schema::hasColumn('kelas', 'tahun_ajaran_id')) {
                // It's good practice to check if the foreign key exists before dropping it,
                // but Laravel's dropForeign handles this reasonably well.
                // The name convention is table_column_foreign
                $table->dropForeign(['tahun_ajaran_id']);
                $table->dropColumn('tahun_ajaran_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            if (!Schema::hasColumn('kelas', 'tahun_ajaran_id')) {
                $table->unsignedBigInteger('tahun_ajaran_id')->nullable()->after('guru_id');
                $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->onDelete('cascade');
            }
        });
    }
};