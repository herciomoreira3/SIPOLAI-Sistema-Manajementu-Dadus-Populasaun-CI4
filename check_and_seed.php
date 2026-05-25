<?php

// Bootstrap CodeIgniter 4
define('FCPATH', __DIR__ . '/public/');
require __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';

$db = \Config\Database::connect();

try {
    // Run migrations first
    echo "Running migrations...\n";
    $runner = \Config\Services::migrations();
    $runner->latest();
    echo "Migrations completed successfully.\n";

    // Check if the population table is empty
    $count = $db->table('tabela_populasaun')->countAllResults();
    if ($count == 0) {
        echo "Database is empty. Seeding database with real population data...\n";
        $seeder = \Config\Database::seeder();
        $seeder->call('RealDataSeeder');
        echo "Database seeded successfully.\n";
    } else {
        echo "Database already has $count records. Skipping seeder to protect live data.\n";
    }
} catch (\Throwable $e) {
    echo "Error during database initialization: " . $e->getMessage() . "\n";
}
