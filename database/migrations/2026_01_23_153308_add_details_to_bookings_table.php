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
        Schema::table('bookings', function (Blueprint $table) {
            // 1. Kita tambahkan 'duration' karena ternyata hilang/belum ada
            // Kita gunakan !Schema::hasColumn untuk pengecekan biar tidak error duplikat
            if (!Schema::hasColumn('bookings', 'duration')) {
                $table->integer('duration')->default(3); 
            }

            // 2. Tambahkan kolom baru (Saya hapus 'after' supaya lebih aman dan pasti jalan)
            $table->integer('total_guests')->default(1);
            $table->string('guest_attire')->nullable();
            $table->string('payment_proof')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['duration', 'total_guests', 'guest_attire', 'payment_proof']);
        });
    }
};