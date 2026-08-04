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
        Schema::create('hasil_status_gizis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengukuran_id')->constrained('pengukurans')->cascadeOnDelete();
            $table->decimal('waz', 5, 2)->nullable();
            $table->decimal('haz', 5, 2)->nullable();
            $table->decimal('whz', 5, 2)->nullable();
            $table->decimal('bmi_z', 5, 2)->nullable();
            $table->string('status_bb_u')->nullable();
            $table->string('status_tb_u')->nullable();
            $table->string('status_bb_tb')->nullable();
            $table->boolean('red_flag')->default(false);
            $table->text('catatan_red_flag')->nullable();
            $table->integer('kkal_kebutuhan')->nullable();
            $table->decimal('bb_ideal', 5, 2)->nullable();
            $table->integer('height_age_bulan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_status_gizis');
    }
};
