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
        Schema::create('who_whz_references', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->decimal('panjang_tinggi', 5, 1); // 45.0, 45.5, ... 120.0
            $table->decimal('L', 10, 4);
            $table->decimal('M', 10, 4);
            $table->decimal('S', 10, 4);
            $table->timestamps();
            
            $table->index(['jenis_kelamin', 'panjang_tinggi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('who_whz_references');
    }
};
