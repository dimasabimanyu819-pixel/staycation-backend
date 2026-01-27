<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('unit_id')->constrained(); // Relasi ke unit
            $table->string('customer_name');
            $table->string('customer_phone');
            
            // WAKTU (INTI SISTEM)
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->dateTime('buffer_end_time'); // Waktu End + Cleaning
            
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            // Index biar pencarian cepat
            $table->index(['start_time', 'buffer_end_time']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};