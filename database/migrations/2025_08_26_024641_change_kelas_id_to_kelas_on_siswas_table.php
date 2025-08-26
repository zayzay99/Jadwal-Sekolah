<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Drop foreign key constraint terlebih dahulu
            if (Schema::hasColumn('siswas', 'kelas_id')) {
                $table->dropForeign(['kelas_id']);
                $table->dropColumn('kelas_id');
            }
            // Tambahkan kolom kelas bertipe string
            $table->string('kelas')->nullable();
        });
    }

    public function down()
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn('kelas');
            $table->bigInteger('kelas_id')->nullable();
            // Jika ingin menambah kembali foreign key, tambahkan di sini
            // $table->foreign('kelas_id')->references('id')->on('kelas');
        });
    }
};