<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Mesin', 'description' => 'Komponen mesin motor Viar'],
            ['name' => 'Kelistrikan', 'description' => 'Komponen kelistrikan motor'],
            ['name' => 'Kaki-kaki', 'description' => 'Komponen kaki-kaki motor'],
            ['name' => 'Body & Aksesoris', 'description' => 'Body motor dan aksesoris'],
            ['name' => 'Oli & Pelumas', 'description' => 'Oli mesin dan pelumas'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
                'is_active' => true,
            ]);
        }
    }
}
