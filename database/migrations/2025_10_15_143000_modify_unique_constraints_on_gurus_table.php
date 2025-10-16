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
        Schema::table('gurus', function (Blueprint $table) {
            // Drop the old unique constraints
            $table->dropUnique('gurus_nip_unique');
            $table->dropUnique('gurus_email_unique');

            // Add new composite unique constraints
            $table->unique(['nip', 'tahun_ajaran_id']);
            $table->unique(['email', 'tahun_ajaran_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            // Drop the new composite unique constraints
            $table->dropUnique(['nip', 'tahun_ajaran_id']);
            $table->dropUnique(['email', 'tahun_ajaran_id']);

            // Re-add the old unique constraints
            $table->unique('nip');
            $table->unique('email');
        });
    }
};
