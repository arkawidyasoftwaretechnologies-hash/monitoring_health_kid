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
        Schema::table('pengukurans', function (Blueprint $table) {
            $table->string('alat_ukur_bb')->nullable()->after('cara_ukur');
            $table->string('alat_ukur_tb')->nullable()->after('alat_ukur_bb');
        });

        Schema::table('hasil_status_gizis', function (Blueprint $table) {
            $table->text('narasi_interpretasi')->nullable()->after('catatan_red_flag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengukurans_and_hasil_status_gizis', function (Blueprint $table) {
            //
        });
    }
};
