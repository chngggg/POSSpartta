<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'target_sales'],
            ['value' => 3000000, 'type' => 'number']
        );
    }
}
