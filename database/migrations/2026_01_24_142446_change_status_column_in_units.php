<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            // Kita ubah tipe kolom 'status' menjadi String bebas (Max 50 karakter)
            // Agar bisa menerima 'available', 'booked', 'maintenance', dll.
            $table->string('status', 50)->change();
        });
    }

    public function down(): void
    {
        // Opsional: Balikin ke asal (tidak wajib diisi untuk fix ini)
    }
};