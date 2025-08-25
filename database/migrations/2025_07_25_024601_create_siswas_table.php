<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('siswas', function (Blueprint $table) {
        $table->id();
        // $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('nama');
        $table->string('nis')->unique();
        $table->string('kelas');
        $table->string('email')->unique();
        $table->string('password');
        $table->timestamps();
    });
}
};