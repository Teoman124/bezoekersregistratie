<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VisitNotificationTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(DatabaseSeeder::class);

        $this->command?->info('Testdata uit DatabaseSeeder is aangemaakt.');
    }
}
