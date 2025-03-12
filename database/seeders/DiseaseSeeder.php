<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiseaseSeeder extends Seeder
{
    public function run()
    {
        DB::table('diseases')->insert([
            ['name' => 'دیابت', 'description' => 'افزایش قند خون به دلیل کمبود انسولین'],
            ['name' => 'فشار خون', 'description' => 'افزایش غیرطبیعی فشار خون'],
            ['name' => 'آنفولانزا', 'description' => 'عفونت ویروسی دستگاه تنفسی'],
        ]);
    }
}
