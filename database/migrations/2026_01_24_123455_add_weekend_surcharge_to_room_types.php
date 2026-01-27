<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            // Kolom untuk simpan nominal kenaikan (default 0)
            $table->decimal('weekend_surcharge', 12, 2)->default(0)->after('price_24_hours');
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn('weekend_surcharge');
        });
    }
};
