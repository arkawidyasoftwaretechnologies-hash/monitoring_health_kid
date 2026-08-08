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
        Schema::table('template_rekomendasis', function (Blueprint $table) {
            $table->string('nama_template')->after('kondisi_pemicu')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_rekomendasis', function (Blueprint $table) {
            $table->dropColumn('nama_template');
        });
    }
};
