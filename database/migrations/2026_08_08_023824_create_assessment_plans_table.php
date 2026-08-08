<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengukuran_id')->constrained('pengukurans')->onDelete('cascade');
            $table->text('draft_otomatis');
            $table->text('assessment_final');
            $table->text('plan_final')->nullable();
            $table->foreignId('disetujui_oleh')->constrained('users');
            $table->timestamp('disetujui_at');
            $table->boolean('dimodifikasi_dari_draft')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_plans');
    }
};
