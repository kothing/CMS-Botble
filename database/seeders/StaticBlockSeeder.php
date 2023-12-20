<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Block\Models\Block;
use Illuminate\Support\Str;

class StaticBlockSeeder extends BaseSeeder
{
    public function run(): void
    {
        Block::query()->truncate();

        $faker = fake();

        for ($i = 0; $i < 5; $i++) {
            $name = $faker->name();

            Block::query()->create([
                'name' => $name,
                'alias' => Str::slug($name),
                'description' => $faker->text(50),
                'content' => $faker->text(500),
            ]);
        }
    }
}
