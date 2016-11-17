<?php

use Illuminate\Database\Seeder;

class CategoriesProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        createCategory(['name' => 'Politik']);
        createCategory(['name' => 'Ekonomi']);
        createCategory(['name' => 'Teknologi']);
        createCategory(['name' => 'Olahraga']);
         createCategory(['name' => 'Hiburan']);
        $featured = createCategory(['name' => 'Pendidikan']);

        //add only one post to featured
        $featured->posts()->attach(\App\Post::latest()->first());

    }
}
