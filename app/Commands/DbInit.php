<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DbInit extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:init';
    protected $description = 'Initializes the database with migrations and seeds if empty.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        try {
            CLI::write('Running migrations...', 'yellow');
            $runner = \Config\Services::migrations();
            $runner->latest();
            CLI::write('Migrations completed successfully.', 'green');

            // Check if population table is empty
            $count = $db->table('tabela_populasaun')->countAllResults();
            if ($count == 0) {
                CLI::write('Database is empty. Seeding database with real population data...', 'yellow');
                $seeder = \Config\Database::seeder();
                $seeder->call('RealDataSeeder');
                CLI::write('Database seeded successfully.', 'green');
            } else {
                CLI::write("Database already has $count records. Skipping seeder to protect live data.", 'cyan');
            }
        } catch (\Throwable $e) {
            CLI::error('Error during database initialization: ' . $e->getMessage());
        }
    }
}
