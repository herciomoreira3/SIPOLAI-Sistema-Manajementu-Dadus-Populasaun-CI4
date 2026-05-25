<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', function() {
    return redirect()->to('/admin');
});

// TEMPORARY DEBUG ROUTE
$routes->get('/force-reset-admin', function() {
    $db = \Config\Database::connect();
    
    echo "<h1>Fixing Database & Permissions...</h1>";
    
    // 1. Delete the admin user we just created to avoid Seeder conflicts
    $db->table('users')->where('email', 'admin@admin.com')->delete();
    $db->table('users')->where('email', 'user@user.com')->delete(); // Just in case
    
    // 2. Clear tables that BoilerplateSeeder seeds so it doesn't crash on unique constraints
    // (We use ignore/skip for groups if they exist, but menus don't have unique constraints so we should truncate menu to avoid duplicates)
    $db->table('menu')->truncate();
    $db->table('groups_menu')->truncate();
    
    // 3. Run the BoilerplateSeeder to seed all Roles, Permissions, and Menus!
    try {
        $seeder = \Config\Database::seeder();
        $seeder->call('Boilerplate\Database\Seeds\BoilerplateSeeder');
        echo "<p style='color:green;'>✅ BoilerplateSeeder ran successfully (Menus and Permissions restored).</p>";
    } catch (\Throwable $e) {
        echo "<p style='color:orange;'>⚠️ Seeder note: " . $e->getMessage() . "</p>";
    }
    
    // 4. Update the admin password to sipolai2026admin
    $hash = \Myth\Auth\Password::hash('sipolai2026admin');
    $db->table('users')->where('email', 'admin@admin.com')->update([
        'password_hash' => $hash,
        'active'        => 1,
        'deleted_at'    => null
    ]);
    echo "<p style='color:green;'>✅ Admin password updated to sipolai2026admin.</p>";
    
    // 5. Test the attempt
    $auth = service('authentication');
    $credentials = ['email' => 'admin@admin.com', 'password' => 'sipolai2026admin'];
    
    if ($auth->attempt($credentials)) {
        echo "<h2 style='color:green;'>AUTH SUCCESS!</h2>";
        echo "<p>Coba login sekarang menggunakan:</p>";
        echo "<ul><li>Email: <b>admin@admin.com</b></li><li>Password: <b>sipolai2026admin</b></li></ul>";
        echo "<p><a href='" . site_url('admin') . "'>Klik di sini untuk masuk ke Dashboard</a></p>";
    } else {
        echo "<h2 style='color:red;'>AUTH FAILED!</h2>";
        echo "<p>Error: " . $auth->error() . "</p>";
        
        echo "<hr><h3>Querying exactly by email:</h3><pre>";
        $byEmail = $db->table('users')->where('email', 'admin@admin.com')->get()->getRowArray();
        print_r($byEmail);
        echo "</pre>";
        
        echo "<hr><h3>Querying exactly by username:</h3><pre>";
        $byUser = $db->table('users')->where('username', 'admin')->get()->getRowArray();
        print_r($byUser);
        echo "</pre>";
    }
    
    exit;
});

$routes->group('admin', ['filter' => 'login'], function($routes) {
    // --- ADMIN ONLY ROUTES ---
    $routes->group('', ['filter' => 'role:admin'], function($routes) {
        $routes->resource('aldeia', ['controller' => '\App\Controllers\Admin\AldeiaController']);
        $routes->resource('profisaun', ['controller' => '\App\Controllers\Admin\ProfisaunController']);
        $routes->resource('relijiaun', ['controller' => '\App\Controllers\Admin\RelijiaunController']);
        $routes->resource('literatura', ['controller' => '\App\Controllers\Admin\LiteraturaController']);
        
        $routes->get('promosaun', '\App\Controllers\Admin\EstruturaSukuController::promosaun', ['as' => 'promosaun']);
        $routes->get('estrutura/users', '\App\Controllers\Admin\EstruturaSukuController::manageUsers', ['as' => 'estrutura-users']);
        $routes->post('estrutura/users/create', '\App\Controllers\Admin\EstruturaSukuController::createUser', ['as' => 'estrutura-users-create']);
        $routes->post('estrutura/users/delete/(:num)', '\App\Controllers\Admin\EstruturaSukuController::deleteUser/$1', ['as' => 'estrutura-users-delete']);
        $routes->post('estrutura/(:num)/delete', '\App\Controllers\Admin\EstruturaSukuController::delete/$1');
        $routes->resource('estrutura', ['controller' => '\App\Controllers\Admin\EstruturaSukuController']);
        $routes->resource('kargu', ['controller' => '\App\Controllers\Admin\KarguController']);

        // Formatu Relatoriu
        $routes->get('formatu-relatoriu', '\App\Controllers\Admin\RelatoriuController::formatuIndex', ['as' => 'formatu-relatoriu']);
        $routes->get('formatu-relatoriu/(:num)/edit', '\App\Controllers\Admin\RelatoriuController::formatuEdit/$1');
        $routes->post('formatu-relatoriu/(:num)/update', '\App\Controllers\Admin\RelatoriuController::formatuUpdate/$1');

        // Tipu & Formatu Deklarasaun
        $routes->get('formatu-deklarasaun', '\App\Controllers\Admin\TipuPediduController::formatuIndex', ['as' => 'formatu-deklarasaun']);
        $routes->get('formatu-deklarasaun/(:num)/edit', '\App\Controllers\Admin\TipuPediduController::formatuEdit/$1');
        $routes->post('formatu-deklarasaun/(:num)/update', '\App\Controllers\Admin\TipuPediduController::formatuUpdate/$1');
    });

    // --- ADMIN & SEKRETARIA ONLY ROUTES ---
    $routes->group('', ['filter' => 'role:admin,sekretaria'], function($routes) {
        $routes->get('inventoriu', '\App\Controllers\Admin\InventoriuController::index', ['as' => 'inventoriu']);
        $routes->delete('inventoriu/(:num)', '\App\Controllers\Admin\InventoriuController::delete/$1');
    });

    // --- GENERAL ACCESSIBLE ROUTES ---
    $routes->resource('populasaun', ['controller' => '\App\Controllers\Admin\PopulasaunController']);
    $routes->post('populasaun/(:num)/status', '\App\Controllers\Admin\PopulasaunController::updateStatus/$1', ['as' => 'populasaun-status']);
    $routes->get('pedidu/populasaun-list', '\App\Controllers\Admin\PediduController::populasaunList', ['as' => 'pedidu-populasaun-list']);
    $routes->get('pedidu/familia-list', '\App\Controllers\Admin\PediduController::familiaList', ['as' => 'pedidu-familia-list']);
    $routes->post('pedidu/create-ajax', '\App\Controllers\Admin\PediduController::createAjax', ['as' => 'pedidu-create-ajax']);
    $routes->get('pedidu/(:num)/print', '\App\Controllers\Admin\PediduController::print/$1', ['as' => 'pedidu-print']);
    $routes->post('pedidu/(:num)/status', '\App\Controllers\Admin\PediduController::updateStatus/$1', ['as' => 'pedidu-status']);
    $routes->resource('pedidu', ['controller' => '\App\Controllers\Admin\PediduController']);
    
    // User Edit Fallback Routes for Boilerplate
    $routes->post('user/manage/(:num)/update', '\Boilerplate\Controllers\Users\UserController::update/$1');
    $routes->post('user/manage/(:num)', '\Boilerplate\Controllers\Users\UserController::update/$1');

    // Estrutura Suku
    $routes->get('hirarkia', '\App\Controllers\Admin\EstruturaSukuController::hirarkia', ['as' => 'hirarkia']);

    // Fixa Familia
    $routes->get('familia/data', '\App\Controllers\Admin\FamiliaController::ajaxData', ['as' => 'familia-data']);
    $routes->post('familia/(:num)/upload-foto', '\App\Controllers\Admin\FamiliaController::uploadFoto/$1', ['as' => 'familia-upload-foto']);
    $routes->post('familia/(:num)/add', '\App\Controllers\Admin\FamiliaController::addMembro/$1', ['as' => 'familia-add-membro']);
    $routes->get('familia/(:num)/remove/(:num)', '\App\Controllers\Admin\FamiliaController::removeMembro/$1/$2', ['as' => 'familia-remove-membro']);
    $routes->resource('familia', ['controller' => '\App\Controllers\Admin\FamiliaController']);

    // Relatoriu
    $routes->get('relatoriu', '\App\Controllers\Admin\RelatoriuController::index', ['as' => 'relatoriu']);
    $routes->get('relatoriu/populasaun', '\App\Controllers\Admin\RelatoriuController::populasaun', ['as' => 'relatoriu-populasaun']);
    $routes->get('relatoriu/familia', '\App\Controllers\Admin\RelatoriuController::familia', ['as' => 'relatoriu-familia']);
    $routes->get('relatoriu/mortalidade', '\App\Controllers\Admin\RelatoriuController::mortalidade', ['as' => 'relatoriu-mortalidade']);
    $routes->get('relatoriu/nascimentu', '\App\Controllers\Admin\RelatoriuController::nascimentu', ['as' => 'relatoriu-nascimentu']);
    $routes->get('relatoriu/muda', '\App\Controllers\Admin\RelatoriuController::muda', ['as' => 'relatoriu-muda']);
    $routes->get('relatoriu/eleitores', '\App\Controllers\Admin\RelatoriuController::eleitores', ['as' => 'relatoriu-eleitores']);
    $routes->get('relatoriu/kbiit-laek', '\App\Controllers\Admin\RelatoriuController::kbiitLaek', ['as' => 'relatoriu-kbiit-laek']);
    $routes->get('relatoriu/pedidu', '\App\Controllers\Admin\RelatoriuController::pedidu', ['as' => 'relatoriu-pedidu']);

    // Dados Eleitores
    $routes->get('eleitores', '\App\Controllers\Admin\EleitorController::index', ['as' => 'eleitores-index']);
    $routes->post('eleitores/(:num)/update', '\App\Controllers\Admin\EleitorController::update/$1', ['as' => 'eleitores-update']);

    // Dados Kbiit Laek
    $routes->get('kbiit-laek', '\App\Controllers\Admin\KbiitLaekController::index', ['as' => 'kbiit-laek-index']);
    $routes->post('kbiit-laek/(:num)/update', '\App\Controllers\Admin\KbiitLaekController::update/$1', ['as' => 'kbiit-laek-update']);
});




