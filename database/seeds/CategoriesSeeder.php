<?php

use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Services',
        ];

        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate([
                'name' => $category,
            ]);
        }
    }
}
