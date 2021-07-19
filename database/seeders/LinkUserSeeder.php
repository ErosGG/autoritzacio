<?php

namespace Database\Seeders;

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Seeder;

class LinkUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        Link::all()->each(
            function ($link) use ($users) {
                $link->users()->attach(
                    $users->random(rand(1, 3))->pluck("id")->toArray()
                );
            }
        );
    }
}
