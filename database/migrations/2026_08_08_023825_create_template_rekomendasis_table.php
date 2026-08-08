<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_rekomendasis', function (Blueprint $table) {
            $table->id();
            $table->string('kondisi_pemicu', 100);
            $table->text('template_assessment');
            $table->text('template_plan');
            $table->tinyInteger('urutan_prioritas')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_rekomendasis');
    }
};
