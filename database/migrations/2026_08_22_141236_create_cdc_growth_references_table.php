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
        Schema::create('cdc_growth_references', function (Blueprint $table) {
            $table->id();
            $table->string('indeks'); // waz, haz, bmiz, whz
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->decimal('usia_bulan', 8, 2); // CDC sometimes uses fractional months, using decimal for precision
            $table->decimal('L', 10, 4);
            $table->decimal('M', 10, 4);
            $table->decimal('S', 10, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cdc_growth_references');
    }
};
