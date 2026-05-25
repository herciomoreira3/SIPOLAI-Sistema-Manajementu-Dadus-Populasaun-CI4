<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MockDataSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Disable Foreign Key checks to truncate cleanly
        $db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $db->table('tabela_populasaun')->truncate();
        $db->table('tabela_familia')->truncate();
        $db->table('tabela_pedidu')->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS = 1;');

        // 2. Fetch all dynamic active master data
        $aldeias = $db->table('tabela_aldeia')->get()->getResultArray();
        if (empty($aldeias)) {
            // Fallback seed master if empty
            $this->call('MasterSeeder');
            $aldeias = $db->table('tabela_aldeia')->get()->getResultArray();
        }
        $aldeiaIds = array_column($aldeias, 'id_aldeia');

        $profisauns = array_column($db->table('tabela_profisaun')->get()->getResultArray(), 'id_profisaun');
        $relijiauns = array_column($db->table('tabela_relijiaun')->get()->getResultArray(), 'id_relijiaun');
        $literaturas = array_column($db->table('tabela_literatura')->get()->getResultArray(), 'id_literatura');

        // Fallbacks if tables are empty
        if (empty($profisauns)) $profisauns = [1, 2, 3, 4, 5];
        if (empty($relijiauns)) $relijiauns = [1, 2, 3, 4];
        if (empty($literaturas)) $literaturas = [1, 2, 3, 4, 5];

        // 3. Name Lists (Portuguese and Tetum names)
        $maleFirstNames = [
            'João', 'Francisco', 'António', 'Manuel', 'José', 'Afonso', 'Domingos', 'Carlos', 'Gabriel', 
            'Bernardo', 'Fernando', 'Mateus', 'Simão', 'Lucas', 'Marcos', 'Roberto', 'Lourenço', 'Vicente', 
            'Alberto', 'Daniel', 'Miguel', 'Pedro', 'Paulo', 'Estevão', 'Tomás', 'Jorge', 'Salvador', 'Abel', 
            'Adriano', 'Agostinho', 'Alexandre', 'Alfredo', 'Álvaro', 'Amílcar', 'Anacleto', 'André', 'Angelo', 
            'Aníbal', 'Arnaldo', 'Artur', 'Augusto', 'Aurélio', 'Bartolomeu', 'Basílio', 'Bento', 'Bonifácio', 
            'Caetano', 'Camilo', 'Cândido', 'Celestino', 'César', 'Cipriano', 'Cláudio', 'Clemente', 'Constantino'
        ];

        $femaleFirstNames = [
            'Maria', 'Ana', 'Rosa', 'Isabel', 'Filomena', 'Madalena', 'Teresa', 'Sofia', 'Rita', 'Clara', 
            'Angela', 'Francisca', 'Joana', 'Domingas', 'Cristina', 'Lucinda', 'Helena', 'Beatriz', 'Marta', 
            'Catarina', 'Jacinta', 'Amélia', 'Aurora', 'Carlota', 'Elisa', 'Glória', 'Adelaide', 'Albertina', 
            'Alda', 'Alice', 'Aliança', 'Alzira', 'Amália', 'Anabela', 'Antónia', 'Arlinda', 'Assunção', 
            'Augusta', 'Aura', 'Áurea', 'Bárbara', 'Belmira', 'Benedita', 'Benigna', 'Berta', 'Bibiana', 
            'Branca', 'Brizida', 'Cândida', 'Carla', 'Carmelita', 'Cármen', 'Carolina', 'Cecília', 'Celeste'
        ];

        $lastNames = [
            'da Costa', 'dos Santos', 'da Silva', 'Rodrigues', 'Guterres', 'Soares', 'Pereira', 'de Araújo', 
            'Pires', 'Ribeiro', 'de Jesus', 'Amaral', 'Xavier', 'Belo', 'Alves', 'Cardozo', 'Mendonça', 'Pinto', 
            'Correia', 'Fonseca', 'Martins', 'Lopes', 'Cabral', 'Carvalho', 'Teixeira', 'Freitas', 'Barbosa', 
            'Barreto', 'Branco', 'Brito', 'Campos', 'Castro', 'Dias', 'Duarte', 'Fernandes', 'Ferreira', 
            'Gomes', 'Gonçalves', 'Guerreiro', 'Henriques', 'Jorge', 'Lima', 'Machado', 'Marques', 'Mendes', 
            'Miranda', 'Moniz', 'Monteiro', 'Moreira', 'Mota', 'Nascimento', 'Neto', 'Nunes', 'Oliveira'
        ];

        $placesOfBirth = [
            'Baucau', 'Dili', 'Quelicai', 'Laisorolai de Baixo', 'Saua-Casa', 'Laga', 'Venilale', 'Viqueque', 
            'Lautem', 'Manatuto', 'Ermera', 'Liquica', 'Bobonaro', 'Cova Lima', 'Ainaro', 'Manufahi', 'Oecusse'
        ];

        $reasonsOfDeath = ['Moras', 'Idade Avançada', 'Adisidente', 'Ataque Kardiaku', 'Moras Pulmaun'];

        // Keep track of unique NIK, Voter card numbers, and KB card numbers
        $usedNik = [];
        $usedEleitoral = [];
        $usedKbiitLaek = [];
        $usedKk = [];

        $populasaunBatch = [];
        $pediduBatch = [];

        $targetTotal = 3000;
        $currentPopCount = 0;

        echo "Hahula seed 3000 dadus populasaun...\n";

        // Loop to generate families until we hit 3000 people exactly
        while ($currentPopCount < $targetTotal) {
            // 4. Create a Family (tabela_familia)
            do {
                $kk = '130304' . mt_rand(1000000000, 9999999999);
            } while (in_array($kk, $usedKk));
            $usedKk[] = $kk;

            $id_aldeia = $aldeiaIds[array_rand($aldeiaIds)];

            $db->table('tabela_familia')->insert([
                'numeru_kk'  => $kk,
                'id_aldeia'  => $id_aldeia,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $id_familia = $db->insertID();

            // Determine size of this family (between 3 and 7 members)
            $familySize = mt_rand(3, 7);
            if ($currentPopCount + $familySize > $targetTotal) {
                $familySize = $targetTotal - $currentPopCount;
            }

            // Generate Xefe Familia details first
            $xefeGender = 'Mane'; // 100% Male heads
            $xefeAge = mt_rand(28, 72);
            $xefeBirthYear = 2026 - $xefeAge;
            $xefeBirthDate = sprintf('%d-%02d-%02d', $xefeBirthYear, mt_rand(1, 12), mt_rand(1, 28));

            $spouseActive = (mt_rand(1, 100) <= 85 && $familySize > 1); // 85% have spouses

            for ($memberIndex = 0; $memberIndex < $familySize; $memberIndex++) {
                $memberGender = 'Mane';
                $memberAge = 20;
                $relasaun = 'Oan';
                $statusKaza = 'Solteiru/a';

                if ($memberIndex === 0) {
                    // Xefe Familia
                    $memberGender = $xefeGender;
                    $memberAge = $xefeAge;
                    $relasaun = 'Xefe Familia';
                    $statusKaza = $spouseActive ? 'Kazaradu/a' : 'Viuvu/a';
                } elseif ($memberIndex === 1 && $spouseActive) {
                    // Spouse
                    $memberGender = ($xefeGender === 'Mane') ? 'Feto' : 'Mane';
                    $memberAge = $xefeAge + mt_rand(-5, 5);
                    if ($memberAge < 18) $memberAge = 18;
                    $relasaun = 'Kônjuge';
                    $statusKaza = 'Kazaradu/a';
                } else {
                    // Children/Other
                    $memberGender = (mt_rand(1, 100) <= 50) ? 'Mane' : 'Feto';
                    // Children ages must be consistent with parents
                    $maxChildAge = $xefeAge - 17;
                    if ($maxChildAge < 0) $maxChildAge = 0;
                    $memberAge = mt_rand(0, $maxChildAge);
                    $relasaun = 'Oan';
                    $statusKaza = 'Solteiru/a';
                }

                // Name Generation
                $firstName = ($memberGender === 'Mane') ? $maleFirstNames[array_rand($maleFirstNames)] : $femaleFirstNames[array_rand($femaleFirstNames)];
                $lastName1 = $lastNames[array_rand($lastNames)];
                $lastName2 = $lastNames[array_rand($lastNames)];
                if ($lastName1 === $lastName2) {
                    $fullName = $firstName . ' ' . $lastName1;
                } else {
                    $fullName = $firstName . ' ' . $lastName1 . ' ' . $lastName2;
                }

                // Make sure name is unique
                $fullName = trim($fullName);

                // Date of Birth
                $birthYear = 2026 - $memberAge;
                $birthDate = sprintf('%d-%02d-%02d', $birthYear, mt_rand(1, 12), mt_rand(1, 28));

                // Unique NIK
                do {
                    $nik = '130304' . mt_rand(100000, 999999) . mt_rand(1000, 9999);
                } while (in_array($nik, $usedNik));
                $usedNik[] = $nik;

                // Status parameters
                $istadu = 'Moris';
                $no_eleitoral = null;
                $no_kbiit_laek = null;

                // Randomly set status and generate corresponding APPROVED requests
                $rand = mt_rand(1, 100);

                if ($memberIndex > 1 && $memberAge == 0 && $rand <= 15) {
                    // Newborn Baby (approved birth certificate request in 2026)
                    $istadu = 'Moris';
                    
                    // Create birth certificate request
                    $pediduBatch[] = [
                        'naran_pedidu' => 'Deklarasaun Nascimentu',
                        'pemohon'      => $fullName,
                        'data_pedidu'  => date('Y-m-d'),
                        'status'       => 'Aprovadu',
                        'id_aldeia'    => $id_aldeia,
                        'meta_data'    => json_encode([
                            'id_familia'    => $id_familia,
                            'jeneru'        => $memberGender,
                            'fatin_moris'   => $placesOfBirth[array_rand($placesOfBirth)],
                            'data_moris'    => $birthDate,
                            'id_relijiaun'  => $relijiauns[array_rand($relijiauns)],
                            'id_profisaun'  => $profisauns[array_rand($profisauns)],
                            'id_literatura' => $literaturas[array_rand($literaturas)],
                            'nik'           => $nik
                        ]),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                } elseif ($memberIndex > 0 && $rand <= 3) {
                    // Deceased member (approved death certificate request)
                    $istadu = 'Mate';
                    
                    // Create death certificate request
                    $deathDate = sprintf('2026-%02d-%02d', mt_rand(1, 5), mt_rand(1, 28));
                    $pediduBatch[] = [
                        'naran_pedidu' => 'Deklarasaun Mortalidade',
                        'pemohon'      => $fullName,
                        'data_pedidu'  => $deathDate,
                        'status'       => 'Aprovadu',
                        'id_aldeia'    => $id_aldeia,
                        'meta_data'    => json_encode([
                            'data_mate' => $deathDate,
                            'kazu_mate' => $reasonsOfDeath[array_rand($reasonsOfDeath)]
                        ]),
                        'created_at' => date('Y-m-d H:i:s', strtotime($deathDate)),
                        'updated_at' => date('Y-m-d H:i:s', strtotime($deathDate)),
                    ];
                } elseif ($memberIndex > 0 && $rand >= 97) {
                    // Moved member (approved domicile change request)
                    $istadu = 'Muda';
                    
                    // Create domicile change request
                    $mudaDate = sprintf('2026-%02d-%02d', mt_rand(1, 5), mt_rand(1, 28));
                    $pediduBatch[] = [
                        'naran_pedidu' => 'Deklarasaun Muda Domisiliu',
                        'pemohon'      => $fullName,
                        'data_pedidu'  => $mudaDate,
                        'status'       => 'Aprovadu',
                        'id_aldeia'    => $id_aldeia,
                        'meta_data'    => null,
                        'created_at' => date('Y-m-d H:i:s', strtotime($mudaDate)),
                        'updated_at' => date('Y-m-d H:i:s', strtotime($mudaDate)),
                    ];
                }

                // If alive, they can be a registered voter or poor family
                if ($istadu === 'Moris') {
                    // Voter (if >= 17 years old)
                    if ($memberAge >= 17) {
                        $voterRand = mt_rand(1, 100);
                        if ($voterRand <= 70) { // 70% of adults are registered voters
                            do {
                                $no_eleitoral = 'EL-' . mt_rand(10000000, 99999999);
                            } while (in_array($no_eleitoral, $usedEleitoral));
                            $usedEleitoral[] = $no_eleitoral;

                            // 15% of these voters have an approved new voter certificate request
                            if ($voterRand <= 15) {
                                $pediduBatch[] = [
                                    'naran_pedidu' => 'Deklarasaun Eleitoral',
                                    'pemohon'      => $fullName,
                                    'data_pedidu'  => sprintf('2026-%02d-%02d', mt_rand(1, 5), mt_rand(1, 28)),
                                    'status'       => 'Aprovadu',
                                    'id_aldeia'    => $id_aldeia,
                                    'meta_data'    => null,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                            }
                        }
                    }

                    // Kbiit Laek (poor/vulnerable)
                    // 10% chance for Xefe Familia or widows to be Kbiit Laek
                    $kbiitRand = mt_rand(1, 100);
                    if (($memberIndex === 0 || $statusKaza === 'Viuvu/a') && $kbiitRand <= 18) {
                        do {
                            $no_kbiit_laek = 'KL-' . mt_rand(10000000, 99999999);
                        } while (in_array($no_kbiit_laek, $usedKbiitLaek));
                        $usedKbiitLaek[] = $no_kbiit_laek;

                        // Create approved Kbiit Laek request
                        $pediduBatch[] = [
                            'naran_pedidu' => 'Deklarasaun Kbiit Laek',
                            'pemohon'      => $fullName,
                            'data_pedidu'  => sprintf('2026-%02d-%02d', mt_rand(1, 5), mt_rand(1, 28)),
                            'status'       => 'Aprovadu',
                            'id_aldeia'    => $id_aldeia,
                            'meta_data'    => null,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                }

                // Choose appropriate Education / Profession based on age
                $id_prof = $profisauns[array_rand($profisauns)];
                $id_lit = $literaturas[array_rand($literaturas)];

                if ($memberAge < 6) {
                    $id_prof = 5; // Desempregadu/unemployed/infant
                    $id_lit = 1;  // Laiha Eskola
                } elseif ($memberAge < 18) {
                    $id_prof = 2; // Estudante
                }

                $populasaunBatch[] = [
                    'nik'              => $nik,
                    'no_eleitoral'     => $no_eleitoral,
                    'no_kbiit_laek'    => $no_kbiit_laek,
                    'naran_kompletu'   => $fullName,
                    'fatin_moris'      => $placesOfBirth[array_rand($placesOfBirth)],
                    'data_moris'       => $birthDate,
                    'jeneru'           => $memberGender,
                    'status_kaza'      => $statusKaza,
                    'id_aldeia'        => $id_aldeia,
                    'id_profisaun'     => $id_prof,
                    'id_relijiaun'     => $relijiauns[array_rand($relijiauns)],
                    'id_literatura'    => $id_lit,
                    'id_familia'       => $id_familia,
                    'relasaun_familia' => $relasaun,
                    'istadu'           => $istadu,
                    'created_at'       => date('Y-m-d H:i:s'),
                    'updated_at'       => date('Y-m-d H:i:s'),
                ];

                $currentPopCount++;

                // Print progress every 500 records
                if ($currentPopCount % 500 === 0) {
                    echo "Hamosu ona dadus populasaun {$currentPopCount} husi 3000...\n";
                }
            }
        }

        // 5. Bulk Insert Population in batches of 500 to keep it extremely memory efficient and fast
        echo "Grava dadus populasaun ba database...\n";
        $popChunks = array_chunk($populasaunBatch, 500);
        foreach ($popChunks as $chunk) {
            $db->table('tabela_populasaun')->insertBatch($chunk);
        }

        // 6. Bulk Insert Pedidu in batches of 500
        echo "Grava dadus pedidu deklarasaun ba database...\n";
        if (!empty($pediduBatch)) {
            $pediduChunks = array_chunk($pediduBatch, 500);
            foreach ($pediduChunks as $chunk) {
                $db->table('tabela_pedidu')->insertBatch($chunk);
            }
        }

        echo "Susesu! Seed total 3000 dadus populasaun real, kria tiha Fixa Familia, ho pedidu deklarasaun sira ne'ebé Aprovadu ona!\n";
    }
}
