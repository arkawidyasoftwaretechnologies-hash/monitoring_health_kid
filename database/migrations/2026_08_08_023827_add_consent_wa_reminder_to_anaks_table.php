<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anaks', function (Blueprint $table) {
            $table->boolean('consent_wa_reminder')->default(false)->after('jenis_kelamin');
        });
    }

    public function down(): void
    {
        Schema::table('anaks', function (Blueprint $table) {
            $table->dropColumn('consent_wa_reminder');
        });
    }
};
