<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_kontrols', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anak_id')->constrained('anaks')->onDelete('cascade');
            $table->foreignId('pengukuran_id')->nullable()->constrained('pengukurans')->onDelete('set null');
            $table->date('tanggal_kontrol_rencana');
            $table->string('nomor_wa_orangtua', 20);
            $table->enum('status_reminder', ['belum_terkirim', 'terkirim', 'gagal'])->default('belum_terkirim');
            $table->timestamp('dikirim_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_kontrols');
    }
};
