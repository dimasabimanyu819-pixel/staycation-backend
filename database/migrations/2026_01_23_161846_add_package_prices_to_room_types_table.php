<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            // Kita tambahkan kolom harga untuk setiap paket
            // Default 0 dulu biar tidak error
            $table->decimal('price_3_hours', 12, 2)->default(0)->after('price');
            $table->decimal('price_6_hours', 12, 2)->default(0)->after('price_3_hours');
            $table->decimal('price_9_hours', 12, 2)->default(0)->after('price_6_hours');
            $table->decimal('price_12_hours', 12, 2)->default(0)->after('price_9_hours');
            $table->decimal('price_24_hours', 12, 2)->default(0)->after('price_12_hours');
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn([
                'price_3_hours', 
                'price_6_hours', 
                'price_9_hours', 
                'price_12_hours', 
                'price_24_hours'
            ]);
        });
    }
};