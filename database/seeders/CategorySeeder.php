<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Tisch',
            'Stuhl',
            'Tablett',
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'uuid' => (string) Str::uuid(),
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
            ]);
        }
    }
}
