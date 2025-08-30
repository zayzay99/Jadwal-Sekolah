<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::create('gurus', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('nip')->unique();
        $table->string('pengampu');
        $table->string('email')->unique();
        $table->string('profile_picture')->nullable();
        $table->string('password');
        $table->timestamps();
    });
}};
