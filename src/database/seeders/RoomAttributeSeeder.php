<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            ['name' => 'トーナメント'],
            ['name' => 'カジュアル'],
            // 必要に応じて追加
        ];

        DB::table('room_attributes')->insert($attributes);
    }
}
