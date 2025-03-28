<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('screenings', function (Blueprint $table) {
        $table->string('type', 20)
              ->default('normal')
              ->after('language');
    });

    // Pour PostgreSQL, ajoutez une contrainte CHECK
    DB::statement("ALTER TABLE screenings ADD CONSTRAINT check_screening_type CHECK (type IN ('normal', 'vip', 'imax', '4dx'))");
}

public function down()
{
    Schema::table('screenings', function (Blueprint $table) {
        $table->dropColumn('type');
    });
}
};
