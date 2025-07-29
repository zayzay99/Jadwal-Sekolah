<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('siswas', function (Blueprint $table) {
            if (Schema::hasColumn('siswas', 'kelas')) {
                $table->dropColumn('kelas');
            }
        });
    }

    public function down()
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('kelas')->nullable();
        });
    }
};
