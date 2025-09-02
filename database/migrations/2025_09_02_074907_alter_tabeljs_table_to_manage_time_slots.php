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
        Schema::table('tabeljs', function (Blueprint $table) {
            // 1. Drop foreign key constraint
            // The default constraint name is <table>_<column>_foreign
            $table->dropForeign(['guru_id']);

            // 2. Drop the unnecessary columns
            $table->dropColumn('nama_pelajaran');
            $table->dropColumn('guru_id');

            // 3. Add new time columns
            $table->time('jam_mulai')->after('jam')->nullable();
            $table->time('jam_selesai')->after('jam_mulai')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabeljs', function (Blueprint $table) {
            // Re-add the columns if migration is rolled back
            $table->string('nama_pelajaran');
            $table->unsignedBigInteger('guru_id')->nullable();
            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('set null');

            // Drop the new time columns
            $table->dropColumn('jam_mulai');
            $table->dropColumn('jam_selesai');
        });
    }
};