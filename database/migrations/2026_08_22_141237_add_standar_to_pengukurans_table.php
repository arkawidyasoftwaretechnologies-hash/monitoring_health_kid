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
            $table->enum('standar_pertumbuhan', ['WHO', 'CDC'])->default('WHO')->after('petugas_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengukurans', function (Blueprint $table) {
            $table->dropColumn('standar_pertumbuhan');
        });
    }
};
