<?php

namespace Database\Seeders;

use App\Models\PostTag;
use Database\Factories\PostTagFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PostTagFactory::times(2000)->create();
    }
}
