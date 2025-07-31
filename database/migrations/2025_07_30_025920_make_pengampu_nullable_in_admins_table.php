<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('pengampu')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('pengampu')->nullable(false)->change();
        });
    }
};