<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Kita tambahkan kolom-kolom yang dikirim oleh Frontend
            
            if (!Schema::hasColumn('bookings', 'duration')) {
                $table->integer('duration')->default(12)->after('room_type_id'); // Durasi (jam)
            }
            if (!Schema::hasColumn('bookings', 'total_guests')) {
                $table->integer('total_guests')->default(1)->after('duration');
            }
            if (!Schema::hasColumn('bookings', 'total_price')) {
                $table->decimal('total_price', 15, 2)->default(0)->after('total_guests');
            }
            if (!Schema::hasColumn('bookings', 'guest_attire')) {
                $table->string('guest_attire')->nullable()->after('customer_phone'); // Ciri pakaian
            }
            if (!Schema::hasColumn('bookings', 'start_time')) {
                $table->dateTime('start_time')->nullable()->after('room_type_id');
            }
            if (!Schema::hasColumn('bookings', 'end_time')) {
                $table->dateTime('end_time')->nullable()->after('start_time');
            }
            
            // Opsional: Status pembayaran
            if (!Schema::hasColumn('bookings', 'payment_status')) {
                $table->string('payment_status')->default('pending'); 
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['duration', 'total_guests', 'total_price', 'guest_attire', 'start_time', 'end_time', 'payment_status']);
        });
    }
};