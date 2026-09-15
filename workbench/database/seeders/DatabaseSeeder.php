<?php

namespace Database\Seeders;

use Heritage\Database\Seeder;
// use Heritage\Database\Console\Seeds\WithoutModelEvents;
use Workbench\Database\Factories\UserFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // UserFactory::new(10)->create();

        UserFactory::new()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
