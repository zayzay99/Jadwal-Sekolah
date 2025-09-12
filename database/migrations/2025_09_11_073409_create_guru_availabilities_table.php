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
        Schema::create('guru_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained()->onDelete('cascade');
            $table->string('hari');
            $table->string('jam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_availabilities');
    }
};
