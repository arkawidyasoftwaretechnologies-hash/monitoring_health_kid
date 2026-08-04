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
        Schema::table('hasil_status_gizis', function (Blueprint $table) {
            $table->decimal('bmiz', 5, 2)->nullable()->after('haz');
            $table->decimal('hcfa', 5, 2)->nullable()->after('bmiz');
            $table->string('status_imt_u')->nullable()->after('status_tb_u');
            $table->string('status_lk_u')->nullable()->after('status_imt_u');
            $table->string('status_lila')->nullable()->after('status_lk_u');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_status_gizis', function (Blueprint $table) {
            $table->dropColumn(['bmiz', 'hcfa', 'status_imt_u', 'status_lk_u', 'status_lila']);
        });
    }
};
