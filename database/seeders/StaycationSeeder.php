<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;
use App\Models\Unit;

class StaycationSeeder extends Seeder
{
    public function run()
    {
        // 1. Buat Tipe Kamar
        $studio = RoomType::create([
            'name' => 'Studio Minimalist',
            'price' => 50000, 
            'description' => 'Cocok untuk transit singkat.',
        ]);

        $luxury = RoomType::create([
            'name' => '2BR Luxury View',
            'price' => 100000, 
            'description' => 'Pemandangan kota, 2 Kamar.',
        ]);

        // 2. Buat Unit Fisik
        Unit::create(['room_type_id' => $studio->id, 'unit_number' => 'A-101']);
        Unit::create(['room_type_id' => $studio->id, 'unit_number' => 'A-102']);
        Unit::create(['room_type_id' => $studio->id, 'unit_number' => 'A-103']);

        Unit::create(['room_type_id' => $luxury->id, 'unit_number' => 'B-201']);
        Unit::create(['room_type_id' => $luxury->id, 'unit_number' => 'B-202']);
    }
}