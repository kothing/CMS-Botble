<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Member\Models\Member;
use Botble\Member\Models\MemberActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends BaseSeeder
{
    public function run(): void
    {
        $files = $this->uploadFiles('members');

        Member::query()->truncate();
        MemberActivityLog::query()->truncate();

        $faker = fake();

        Member::query()->create([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@botble.com',
            'password' => Hash::make('12345678'),
            'dob' => $faker->dateTime(),
            'phone' => $faker->phoneNumber(),
            'avatar_id' => ! $files[0]['error'] ? $files[0]['data']->id : 0,
            'description' => $faker->realText(30),
            'confirmed_at' => Carbon::now(),
        ]);

        for ($i = 0; $i < 9; $i++) {
            Member::query()->create([
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'email' => $faker->email(),
                'password' => Hash::make('12345678'),
                'dob' => $faker->dateTime(),
                'phone' => $faker->phoneNumber(),
                'avatar_id' => ! $files[$i + 1]['error'] ? $files[$i + 1]['data']->id : 0,
                'description' => $faker->realText(30),
                'confirmed_at' => Carbon::now(),
            ]);
        }
    }
}
