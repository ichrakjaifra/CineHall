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
      Schema::create('halls', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->integer('capacity');
        $table->enum('type', ['normal', 'vip', 'imax', '4dx']);
        $table->json('seat_map')->nullable(); // Configuration des sièges
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('halls');
    }
};
