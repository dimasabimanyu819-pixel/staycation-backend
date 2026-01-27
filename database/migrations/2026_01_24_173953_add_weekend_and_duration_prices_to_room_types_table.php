<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('room_types', function (Blueprint $table) {
            // Kita tambahkan kolom-kolom harga baru
            // 'nullable' artinya boleh kosong (biar tidak error kalau belum diisi)
            
            if (!Schema::hasColumn('room_types', 'weekend_price')) {
                $table->integer('weekend_price')->default(0)->after('price');
            }

            if (!Schema::hasColumn('room_types', 'price_3_hours')) {
                $table->integer('price_3_hours')->nullable()->after('weekend_price');
            }
            if (!Schema::hasColumn('room_types', 'price_6_hours')) {
                $table->integer('price_6_hours')->nullable()->after('price_3_hours');
            }
            if (!Schema::hasColumn('room_types', 'price_9_hours')) {
                $table->integer('price_9_hours')->nullable()->after('price_6_hours');
            }
            if (!Schema::hasColumn('room_types', 'price_12_hours')) {
                $table->integer('price_12_hours')->nullable()->after('price_9_hours');
            }
            if (!Schema::hasColumn('room_types', 'price_24_hours')) {
                $table->integer('price_24_hours')->nullable()->after('price_12_hours');
            }
        });
    }

    public function down()
    {
        Schema::table('room_types', function (Blueprint $table) {
            // Hapus kolom kalau migrasi dibatalkan
            $table->dropColumn([
                'weekend_price',
                'price_3_hours', 
                'price_6_hours', 
                'price_9_hours', 
                'price_12_hours', 
                'price_24_hours'
            ]);
        });
    }
};