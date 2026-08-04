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
        Schema::create('who_growth_references', function (Blueprint $table) {
            $table->id();
            $table->string('indeks'); // waz, haz, whz, bmiz
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->integer('usia_bulan');
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
        Schema::dropIfExists('who_growth_references');
    }
};
