<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables([
            "links",
            "users",
        ]);
        $this->call([
            LinkSeeder::class,
            UserSeeder::class,
            LinkUserSeeder::class,
        ]);
    }

    protected function truncateTables(array $tables)
    {
        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    }
}
