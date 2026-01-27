<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            // CEK DULU: Hanya buat jika kolom BELUM ada
            if (!Schema::hasColumn('room_types', 'price_3_hours')) {
                $table->integer('price_3_hours')->default(0)->after('price');
            }
            if (!Schema::hasColumn('room_types', 'price_6_hours')) {
                $table->integer('price_6_hours')->default(0)->after('price');
            }
            if (!Schema::hasColumn('room_types', 'price_9_hours')) {
                $table->integer('price_9_hours')->default(0)->after('price');
            }
            if (!Schema::hasColumn('room_types', 'price_12_hours')) {
                $table->integer('price_12_hours')->default(0)->after('price');
            }
            if (!Schema::hasColumn('room_types', 'price_24_hours')) {
                $table->integer('price_24_hours')->default(0)->after('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            // Hapus kolom hanya jika ada
            $columns = ['price_3_hours', 'price_6_hours', 'price_9_hours', 'price_12_hours', 'price_24_hours'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('room_types', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};