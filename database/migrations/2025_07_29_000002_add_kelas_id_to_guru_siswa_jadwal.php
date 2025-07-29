<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->unsignedBigInteger('kelas_id')->nullable()->after('id');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('set null');
        });
        Schema::table('siswas', function (Blueprint $table) {
            $table->unsignedBigInteger('kelas_id')->nullable()->after('id');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('set null');
        });
        Schema::table('jadwals', function (Blueprint $table) {
            $table->unsignedBigInteger('kelas_id')->nullable()->after('id');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
    }
};
