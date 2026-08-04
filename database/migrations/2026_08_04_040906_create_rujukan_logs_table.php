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
        Schema::create('rujukan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anak_id')->constrained('anaks')->cascadeOnDelete();
            $table->foreignId('pengukuran_id')->nullable()->constrained('pengukurans')->nullOnDelete();
            $table->date('tanggal_rujukan');
            $table->string('tujuan_rujukan'); // misal: RSUD, Dokter Spesialis
            $table->text('alasan_rujukan');
            $table->string('status_tindak_lanjut')->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rujukan_logs');
    }
};
