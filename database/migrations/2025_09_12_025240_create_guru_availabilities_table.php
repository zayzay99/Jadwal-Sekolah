<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuruAvailabilitiesTable extends Migration
{
    public function up()
    {
        Schema::create('guru_availabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guru_id');
            $table->string('hari');
            $table->string('jam_mulai');
            $table->string('jam_selesai');
            $table->timestamps();

            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('guru_availabilities');
    }
}