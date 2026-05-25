<?php
// Load CodeIgniter boot
define('FCPATH', __DIR__ . '/public/');
require __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';
CodeIgniter\Boot::bootTest($paths);

$auth = service('authentication');
$userModel = model('UserModel');

echo "=== TESTING LOGIN FOR ADMIN ===\n";
$user = $userModel->where('username', 'admin')->first();
if (!$user) {
    echo "Admin user not found in database!\n";
} else {
    echo "Admin found. ID: " . $user->id . "\n";
    echo "Password Hash in DB: " . $user->password_hash . "\n";
    echo "Active Status: " . $user->active . "\n";
    
    // Test validation
    $valid = $auth->validate([
        'login' => 'admin',
        'password' => 'admin123'
    ]);
    if ($valid) {
        echo "VALIDATION SUCCESSFUL!\n";
    } else {
        echo "VALIDATION FAILED!\n";
        echo "Error: " . $auth->error() . "\n";
    }
}
