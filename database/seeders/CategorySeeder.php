<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Web Development',  'slug' => 'web-development'],
            ['name' => 'Data Science',     'slug' => 'data-science'],
            ['name' => 'Mobile Dev',       'slug' => 'mobile-dev'],
            ['name' => 'Algorithms',       'slug' => 'algorithms'],
            ['name' => 'Databases',        'slug' => 'databases'],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
