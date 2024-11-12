<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            ['name' => 'アドバイス歓迎'],
            ['name' => 'アドバイス不要'],
            ['name' => 'エンジョイ'],
            ['name' => '操作練習中'],
            ['name' => '新キャラクター練習中'],
            ['name' => '初心者歓迎'],
            ['name' => '初級者歓迎'],
            ['name' => '中級者歓迎'],
            ['name' => '上級者歓迎'],
            ['name' => '実力が近い人歓迎'],
            ['name' => 'ランクしながら待機中'],
            ['name' => '配信中']
        ];

        DB::table('room_attributes')->insert($attributes);
    }
}
