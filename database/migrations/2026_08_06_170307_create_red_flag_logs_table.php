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
        Schema::create('red_flag_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengukuran_id')->constrained()->cascadeOnDelete();
            $table->foreignId('anak_id')->constrained()->cascadeOnDelete();
            
            $table->enum('kategori_flag', [
                'gizi_buruk_akut',        // WHZ < -3
                'gizi_kurang_akut',       // -3 <= WHZ < -2
                'stunting_berat',         // HAZ < -3
                'stunting',               // -3 <= HAZ < -2
                'underweight_berat',      // WAZ < -3
                'underweight',            // -3 <= WAZ < -2
                'overweight',             // WHZ atau BMIZ > 2
                'obesitas',               // WHZ atau BMIZ > 3
                'lila_gizi_buruk',        // LiLA < 11.5 cm (6-59 bln)
                'lila_gizi_kurang',       // 11.5 <= LiLA < 12.5 cm
                'growth_faltering',       // kenaikan BB < velocity standar WHO 2 periode berturut
                'mikrosefali',            // HCZ < -2
                'makrosefali'             // HCZ > 2
            ]);
            $table->enum('severity', ['kuning', 'merah']);
            $table->decimal('nilai_pemicu', 6, 2);
            $table->string('rekomendasi_rujukan')->nullable();
            $table->enum('status', ['baru', 'ditinjau', 'dirujuk', 'selesai'])->default('baru');

            $table->timestamps();

            $table->index(['anak_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('red_flag_logs');
    }
};
