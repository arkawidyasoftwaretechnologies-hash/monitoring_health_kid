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
        Schema::create('rda_per_kgbbs', function (Blueprint $table) {
            $table->id();
            $table->integer('usia_bulan_min');
            $table->integer('usia_bulan_max');
            $table->integer('rda_kkal_per_kgbb');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rda_per_kgbbs');
    }
};
