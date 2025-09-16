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
            $table->foreignId('jadwal_kategori_id')->nullable()->constrained('jadwal_kategoris')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabeljs', function (Blueprint $table) {
            $table->dropForeign(['jadwal_kategori_id']);
            $table->dropColumn('jadwal_kategori_id');
        });
    }
};
