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
            $table->decimal('lingkar_kepala', 5, 2)->nullable()->after('tinggi_badan');
            $table->decimal('lila', 5, 2)->nullable()->after('lingkar_kepala');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengukurans', function (Blueprint $table) {
            $table->dropColumn(['lingkar_kepala', 'lila']);
        });
    }
};
