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
        Schema::table('who_whz_references', function (Blueprint $table) {
            $table->enum('tipe_ukur', ['panjang', 'tinggi'])->default('panjang')->after('panjang_tinggi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('who_whz_references', function (Blueprint $table) {
            $table->dropColumn('tipe_ukur');
        });
    }
};
