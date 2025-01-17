<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Makanan Berat', 'description' => 'Makanan Mengenyangkan'],
            ['name' => 'Makanan Ringan', 'description' => 'Makanan Pendamping'],
            ['name' => 'Makanan Penutup', 'description' => 'Makanan Manis'],
            ['name' => 'Minuman', 'description' => 'Minuman Segar']
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}