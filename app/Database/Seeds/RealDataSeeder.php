<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * RealDataSeeder
 *
 * Seeds the database with REAL population data for Suku Laisorulai de Baixo.
 * Includes adjustments for specific religion, profession, and education targets,
 * as well as generating electoral numbers for all adults and Pedidu history for 
 * population status changes (Moris -> Mate/Muda).
 */
class RealDataSeeder extends Seeder
{
    public function run()
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // =====================================================================
        // 1. CLEAR EXISTING DATA
        // =====================================================================
        $db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $db->table('tabela_estrutura_suku')->truncate();
        $db->table('tabela_populasaun')->truncate();
        $db->table('tabela_familia')->truncate();
        $db->table('tabela_pedidu')->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS = 1;');
        echo "Dados antigos sira hetan hamoos ona.\n";

        // =====================================================================
        // 2. FIX ALDEIA NAMES
        // =====================================================================
        $correctAldeias = ['Dara-oma', 'Legu', 'Sau-casa', 'Ulu-soru'];
        $existingAldeias = $db->table('tabela_aldeia')->orderBy('id_aldeia', 'ASC')->get()->getResultArray();
        $existingNames = array_column($existingAldeias, 'naran_aldeia');
        
        if (array_diff($correctAldeias, $existingNames)) {
            if (count($existingAldeias) >= 4) {
                $updates = [
                    $existingAldeias[0]['id_aldeia'] => 'Dara-oma',
                    $existingAldeias[1]['id_aldeia'] => 'Legu',
                    $existingAldeias[2]['id_aldeia'] => 'Sau-casa',
                    $existingAldeias[3]['id_aldeia'] => 'Ulu-soru',
                ];
                foreach ($updates as $id => $name) {
                    $db->table('tabela_aldeia')->where('id_aldeia', $id)->update(['naran_aldeia' => $name]);
                }
            } else {
                $db->query('SET FOREIGN_KEY_CHECKS = 0;');
                $db->table('tabela_aldeia')->truncate();
                $db->query('SET FOREIGN_KEY_CHECKS = 1;');
                $db->table('tabela_aldeia')->insertBatch([
                    ['naran_aldeia' => 'Dara-oma', 'id_suku' => 1, 'created_at' => $now, 'updated_at' => $now],
                    ['naran_aldeia' => 'Legu',     'id_suku' => 1, 'created_at' => $now, 'updated_at' => $now],
                    ['naran_aldeia' => 'Sau-casa', 'id_suku' => 1, 'created_at' => $now, 'updated_at' => $now],
                    ['naran_aldeia' => 'Ulu-soru', 'id_suku' => 1, 'created_at' => $now, 'updated_at' => $now],
                ]);
            }
        }

        // =====================================================================
        // 3. ENSURE MISSING MASTER DATA EXISTS (Islam, Veteranos, etc.)
        // =====================================================================
        $this->ensureMasterDataExists($db, 'tabela_relijiaun', 'naran_relijiaun', 'Islam');
        $this->ensureMasterDataExists($db, 'tabela_relijiaun', 'naran_relijiaun', 'Protestante');
        $this->ensureMasterDataExists($db, 'tabela_profisaun', 'naran_profisaun', 'Veteranos');
        $this->ensureMasterDataExists($db, 'tabela_literatura', 'naran_literatura', 'Mestradu');
        $this->ensureMasterDataExists($db, 'tabela_literatura', 'naran_literatura', 'Pre Secundariu');

        // =====================================================================
        // 4. LOOKUP MASTER IDs
        // =====================================================================
        $aldeiaMap = array_column($db->table('tabela_aldeia')->get()->getResultArray(), 'id_aldeia', 'naran_aldeia');
        // Lowercase keys for safety
        $aldeiaMap = array_change_key_case($aldeiaMap, CASE_LOWER);
        $idDaraOma = $aldeiaMap['dara-oma'] ?? null;
        $idLegu    = $aldeiaMap['legu'] ?? null;
        $idSauCasa = $aldeiaMap['sau-casa'] ?? null;
        $idUluSoru = $aldeiaMap['ulu-soru'] ?? null;

        $profMap = array_change_key_case(array_column($db->table('tabela_profisaun')->get()->getResultArray(), 'id_profisaun', 'naran_profisaun'), CASE_LOWER);
        $pAgrikultor   = $this->findId($profMap, ['agrikultór','agrikultor'], 1);
        $pEstudante    = $this->findId($profMap, ['estudante'], 2);
        $pNegosiante   = $this->findId($profMap, ['negosiante'], 3);
        $pFunsiPubliku = $this->findId($profMap, ['funsonáriu publiku','funsionariu publiku'], 4);
        $pDesempregadu = $this->findId($profMap, ['desempregadu'], 5);
        $pVeteranos    = $this->findId($profMap, ['veteranos'], 6);

        $relijMap = array_change_key_case(array_column($db->table('tabela_relijiaun')->get()->getResultArray(), 'id_relijiaun', 'naran_relijiaun'), CASE_LOWER);
        $rKatolika = $this->findId($relijMap, ['katólika','katolika'], 1);
        $rProtestante = $this->findId($relijMap, ['protestante'], 2);
        $rIslam    = $this->findId($relijMap, ['islam'], 3);

        $litMap = array_change_key_case(array_column($db->table('tabela_literatura')->get()->getResultArray(), 'id_literatura', 'naran_literatura'), CASE_LOWER);
        $lLaihaEskola  = $this->findId($litMap, ['laiha eskola'], 1);
        $lEnsinuBaziku = $this->findId($litMap, ['ensinu báziku','ensinu baziku'], 2);
        $lPreSecundariu= $this->findId($litMap, ['pre secundariu','pre-sekundáriu'], 6);
        $lSecundariu   = $this->findId($litMap, ['ensinu sekundáriu','ensinu sekundariu'], 3);
        $lLisensiatura = $this->findId($litMap, ['lisensiatura'], 4);
        $lMestradu     = $this->findId($litMap, ['mestradu'], 5);

        // =====================================================================
        // 5. UNIQUE ID TRACKERS & HELPERS
        // =====================================================================
        $usedNik       = [];
        $usedEleitoral = [];
        $usedKk        = [];

        $genNik = function () use (&$usedNik) {
            do { $nik = '130304' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT) . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT); } while (in_array($nik, $usedNik));
            $usedNik[] = $nik;
            return $nik;
        };

        $genKk = function () use (&$usedKk) {
            do { $kk = '130304' . mt_rand(1000000000, 9999999999); } while (in_array($kk, $usedKk));
            $usedKk[] = $kk;
            return $kk;
        };

        $genEleitoral = function () use (&$usedEleitoral) {
            do { $no = 'EL-' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT); } while (in_array($no, $usedEleitoral));
            $usedEleitoral[] = $no;
            return $no;
        };

        $usedKbiit = [];
        $genKbiit = function () use (&$usedKbiit) {
            do { $no = 'KB-' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT); } while (in_array($no, $usedKbiit));
            $usedKbiit[] = $no;
            return $no;
        };

        // =====================================================================
        // 6. INSERT REAL INDIVIDUALS FROM pop.docx + ESTRUTURA SUKU
        // =====================================================================
        $estruturaBatch = [];
        $insertPerson = function (array $data) use ($db, $now) {
            $data['created_at'] = $now;
            $data['updated_at'] = $now;
            $db->table('tabela_populasaun')->insert($data);
            return $db->insertID();
        };

        $addEstrutura = function (int $idPop, int $idAldeia, string $naran, string $kargu) use (&$estruturaBatch, $now) {
            $estruturaBatch[] = [
                'id_populasaun'  => $idPop, 'id_aldeia'      => $idAldeia,
                'naran_membru'   => $naran, 'kargu'          => $kargu,
                'periodo_hahula' => '2022-05-20', 'periodo_remata' => null,
                'status_kargu'   => 'Ativu', 'created_at'     => $now, 'updated_at'     => $now,
            ];
        };

        // --- ULU-SORU ---
        $famU1 = $db->table('tabela_familia')->insert(['numeru_kk' => $genKk(), 'id_aldeia' => $idUluSoru]); $famU1 = $db->insertID();
        $idAmerco = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Amerco Fernandes', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1972-06-15', 'jeneru' => 'Mane', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idUluSoru, 'id_profisaun' => $pFunsiPubliku, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famU1, 'relasaun_familia' => 'Xefe Familia', 'istadu' => 'Moris']);
        $addEstrutura($idAmerco, $idUluSoru, 'Amerco Fernandes', 'Xefe Aldeia');
        $idMarquita = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Marquita dos Santos', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1975-03-20', 'jeneru' => 'Feto', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idUluSoru, 'id_profisaun' => $pAgrikultor, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lEnsinuBaziku, 'id_familia' => $famU1, 'relasaun_familia' => 'Kônjuge', 'istadu' => 'Moris']);
        $addEstrutura($idMarquita, $idUluSoru, 'Marquita dos Santos', 'Delegada');
        $idJuliao = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Julião Pinto Belo', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '2000-07-14', 'jeneru' => 'Mane', 'status_kaza' => 'Solteiru/a', 'id_aldeia' => $idUluSoru, 'id_profisaun' => $pEstudante, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famU1, 'relasaun_familia' => 'Oan', 'istadu' => 'Moris']);
        $addEstrutura($idJuliao, $idUluSoru, 'Julião Pinto Belo', 'Delegado');

        // --- SAU-CASA ---
        $famS1 = $db->table('tabela_familia')->insert(['numeru_kk' => $genKk(), 'id_aldeia' => $idSauCasa]); $famS1 = $db->insertID();
        $usedEleitoral[] = '000117412';
        $idJulio = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => '000117412', 'naran_kompletu' => 'Julio Farkus Pinto', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1968-04-10', 'jeneru' => 'Mane', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idSauCasa, 'id_profisaun' => $pFunsiPubliku, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lLisensiatura, 'id_familia' => $famS1, 'relasaun_familia' => 'Xefe Familia', 'istadu' => 'Moris']);
        $addEstrutura($idJulio, $idSauCasa, 'Julio Farkus Pinto', 'Xefe Suku');

        $famS2 = $db->table('tabela_familia')->insert(['numeru_kk' => $genKk(), 'id_aldeia' => $idSauCasa]); $famS2 = $db->insertID();
        $idMummhad = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Mummhad Hidayat', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1975-11-05', 'jeneru' => 'Mane', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idSauCasa, 'id_profisaun' => $pFunsiPubliku, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famS2, 'relasaun_familia' => 'Xefe Familia', 'istadu' => 'Moris']);
        $addEstrutura($idMummhad, $idSauCasa, 'Mummhad Hidayat', 'Xefe Aldeia');
        $idMargarida = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Margarida Fatima', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '2002-08-22', 'jeneru' => 'Feto', 'status_kaza' => 'Solteiru/a', 'id_aldeia' => $idSauCasa, 'id_profisaun' => $pEstudante, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famS2, 'relasaun_familia' => 'Oan', 'istadu' => 'Moris']);
        $addEstrutura($idMargarida, $idSauCasa, 'Margarida Fatima', 'Delegada');
        $idVeronica = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Veronica da Costa Alves', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '2000-03-17', 'jeneru' => 'Feto', 'status_kaza' => 'Solteiru/a', 'id_aldeia' => $idSauCasa, 'id_profisaun' => $pFunsiPubliku, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famS2, 'relasaun_familia' => 'Oan', 'istadu' => 'Moris']);
        $addEstrutura($idVeronica, $idSauCasa, 'Veronica da Costa Alves', 'Xefe Juventude');

        $famS3 = $db->table('tabela_familia')->insert(['numeru_kk' => $genKk(), 'id_aldeia' => $idSauCasa]); $famS3 = $db->insertID();
        $idGrigorio = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Grigorio Guterres', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1973-09-05', 'jeneru' => 'Mane', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idSauCasa, 'id_profisaun' => $pFunsiPubliku, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famS3, 'relasaun_familia' => 'Xefe Familia', 'istadu' => 'Moris']);
        $addEstrutura($idGrigorio, $idSauCasa, 'Grigorio Guterres', 'Delegado');

        // --- LEGU ---
        $famL1 = $db->table('tabela_familia')->insert(['numeru_kk' => $genKk(), 'id_aldeia' => $idLegu]); $famL1 = $db->insertID();
        $idHipolito = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Hipolito Cabral', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1971-03-08', 'jeneru' => 'Mane', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idLegu, 'id_profisaun' => $pFunsiPubliku, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famL1, 'relasaun_familia' => 'Xefe Familia', 'istadu' => 'Moris']);
        $addEstrutura($idHipolito, $idLegu, 'Hipolito Cabral', 'Xefe Aldeia');

        $famL2 = $db->table('tabela_familia')->insert(['numeru_kk' => $genKk(), 'id_aldeia' => $idLegu]); $famL2 = $db->insertID();
        $idFaustino = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Faustino F. Cabral', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1976-07-11', 'jeneru' => 'Mane', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idLegu, 'id_profisaun' => $pFunsiPubliku, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famL2, 'relasaun_familia' => 'Xefe Familia', 'istadu' => 'Moris']);
        $addEstrutura($idFaustino, $idLegu, 'Faustino F. Cabral', 'Delegado');
        $idBelina = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Belina D. Cabral', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1979-12-03', 'jeneru' => 'Feto', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idLegu, 'id_profisaun' => $pAgrikultor, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lEnsinuBaziku, 'id_familia' => $famL2, 'relasaun_familia' => 'Kônjuge', 'istadu' => 'Moris']);
        $addEstrutura($idBelina, $idLegu, 'Belina D. Cabral', 'Delegada');

        $famL3 = $db->table('tabela_familia')->insert(['numeru_kk' => $genKk(), 'id_aldeia' => $idLegu]); $famL3 = $db->insertID();
        $idManuel = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Manuel da Costa Pinto', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1982-02-14', 'jeneru' => 'Mane', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idLegu, 'id_profisaun' => $pFunsiPubliku, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famL3, 'relasaun_familia' => 'Xefe Familia', 'istadu' => 'Moris']);
        $addEstrutura($idManuel, $idLegu, 'Manuel da Costa Pinto', 'Xefe Juventude');

        // --- DARA-OMA ---
        $famD1 = $db->table('tabela_familia')->insert(['numeru_kk' => $genKk(), 'id_aldeia' => $idDaraOma]); $famD1 = $db->insertID();
        $idCandido = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Candido da C. Jeronimo', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1969-11-20', 'jeneru' => 'Mane', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pFunsiPubliku, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famD1, 'relasaun_familia' => 'Xefe Familia', 'istadu' => 'Moris']);
        $addEstrutura($idCandido, $idDaraOma, 'Candido da C. Jeronimo', 'Xefe Aldeia');
        $idJulieta = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Julieta da Silva Faria', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1973-04-08', 'jeneru' => 'Feto', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pAgrikultor, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lEnsinuBaziku, 'id_familia' => $famD1, 'relasaun_familia' => 'Kônjuge', 'istadu' => 'Moris']);
        $addEstrutura($idJulieta, $idDaraOma, 'Julieta da Silva Faria', 'Delegada');

        $famD2 = $db->table('tabela_familia')->insert(['numeru_kk' => $genKk(), 'id_aldeia' => $idDaraOma]); $famD2 = $db->insertID();
        $idCrizago = $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Crizago Aleixo da Costa', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1971-08-17', 'jeneru' => 'Mane', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pFunsiPubliku, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famD2, 'relasaun_familia' => 'Xefe Familia', 'istadu' => 'Moris']);
        $addEstrutura($idCrizago, $idDaraOma, 'Crizago Aleixo da Costa', 'Delegado');
        $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Olivia F. Freitas', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1975-05-22', 'jeneru' => 'Feto', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pAgrikultor, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lEnsinuBaziku, 'id_familia' => $famD2, 'relasaun_familia' => 'Kônjuge', 'istadu' => 'Moris']);
        $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Javio J. Freitas', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '2006-03-11', 'jeneru' => 'Mane', 'status_kaza' => 'Solteiru/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pEstudante, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lEnsinuBaziku, 'id_familia' => $famD2, 'relasaun_familia' => 'Oan', 'istadu' => 'Moris']);
        $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Sartes J. Freitas', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '2008-07-25', 'jeneru' => 'Mane', 'status_kaza' => 'Solteiru/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pEstudante, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lEnsinuBaziku, 'id_familia' => $famD2, 'relasaun_familia' => 'Oan', 'istadu' => 'Moris']);
        $insertPerson(['nik' => $genNik(), 'naran_kompletu' => 'Quencia J. Freitas', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '2010-01-17', 'jeneru' => 'Feto', 'status_kaza' => 'Solteiru/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pEstudante, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lPreSecundariu, 'id_familia' => $famD2, 'relasaun_familia' => 'Oan', 'istadu' => 'Moris']);
        $insertPerson(['nik' => $genNik(), 'naran_kompletu' => 'Fevri J. Freitas', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '2013-09-30', 'jeneru' => 'Feto', 'status_kaza' => 'Solteiru/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pEstudante, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lPreSecundariu, 'id_familia' => $famD2, 'relasaun_familia' => 'Oan', 'istadu' => 'Moris']);

        $famD3 = $db->table('tabela_familia')->insert(['numeru_kk' => $genKk(), 'id_aldeia' => $idDaraOma]); $famD3 = $db->insertID();
        $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Pedro da C. Cabral', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1965-04-20', 'jeneru' => 'Mane', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pVeteranos, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famD3, 'relasaun_familia' => 'Xefe Familia', 'istadu' => 'Moris']);
        $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Luisa M. Freitas', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1970-06-14', 'jeneru' => 'Feto', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pAgrikultor, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lEnsinuBaziku, 'id_familia' => $famD3, 'relasaun_familia' => 'Kônjuge', 'istadu' => 'Moris']);
        $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Agusto C. da Costa', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '2003-11-02', 'jeneru' => 'Mane', 'status_kaza' => 'Solteiru/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pEstudante, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famD3, 'relasaun_familia' => 'Oan', 'istadu' => 'Moris']);
        $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Hermina F. Cabral', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '2001-08-15', 'jeneru' => 'Feto', 'status_kaza' => 'Solteiru/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pFunsiPubliku, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lLisensiatura, 'id_familia' => $famD3, 'relasaun_familia' => 'Oan', 'istadu' => 'Moris']);

        $famD4 = $db->table('tabela_familia')->insert(['numeru_kk' => $genKk(), 'id_aldeia' => $idDaraOma]); $famD4 = $db->insertID();
        $insertPerson(['nik' => $genNik(), 'no_eleitoral' => $genEleitoral(), 'naran_kompletu' => 'Paulo Cabral', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '1990-11-06', 'jeneru' => 'Mane', 'status_kaza' => 'Kazaradu/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pFunsiPubliku, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lSecundariu, 'id_familia' => $famD4, 'relasaun_familia' => 'Xefe Familia', 'istadu' => 'Moris']);
        $insertPerson(['nik' => $genNik(), 'naran_kompletu' => 'Adelaide dos Santos', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '2018-02-08', 'jeneru' => 'Feto', 'status_kaza' => 'Solteiru/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pDesempregadu, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lLaihaEskola, 'id_familia' => $famD4, 'relasaun_familia' => 'Oan', 'istadu' => 'Moris']);
        $insertPerson(['nik' => $genNik(), 'naran_kompletu' => 'Alberto Cabral', 'fatin_moris' => 'Laisorulai de Baixo', 'data_moris' => '2017-10-14', 'jeneru' => 'Mane', 'status_kaza' => 'Solteiru/a', 'id_aldeia' => $idDaraOma, 'id_profisaun' => $pDesempregadu, 'id_relijiaun' => $rKatolika, 'id_literatura' => $lLaihaEskola, 'id_familia' => $famD4, 'relasaun_familia' => 'Oan', 'istadu' => 'Moris']);

        if (!empty($estruturaBatch)) {
            $db->table('tabela_estrutura_suku')->insertBatch($estruturaBatch);
        }

        // =====================================================================
        // 7. GENERATE SYNTHETIC POPULATION (Meeting the stats precisely)
        // =====================================================================
        $realM = []; $realF = []; $realFam = [];
        foreach ([$idDaraOma, $idLegu, $idSauCasa, $idUluSoru] as $aId) {
            $realM[$aId]   = (int) $db->table('tabela_populasaun')->where('id_aldeia', $aId)->where('jeneru', 'Mane')->countAllResults();
            $realF[$aId]   = (int) $db->table('tabela_populasaun')->where('id_aldeia', $aId)->where('jeneru', 'Feto')->countAllResults();
            $realFam[$aId] = (int) $db->table('tabela_familia')->where('id_aldeia', $aId)->countAllResults();
        }

        $ageRanges = [[2023, 2026], [2021, 2022], [2014, 2020], [2006, 2013], [2001, 2005], [1996, 2000], [1940, 1995]];
        $aldeiaConfig = [
            $idDaraOma => ['targetFam' => 123, 'ageM' => [4, 3, 10, 2, 9, 10, 195], 'ageF' => [3, 9, 3, 3, 3, 4, 200]],
            $idLegu    => ['targetFam' => 47,  'ageM' => [2, 8, 15, 8, 3, 22, 36],  'ageF' => [3, 14, 9, 3, 9, 8, 65]],
            $idSauCasa => ['targetFam' => 102, 'ageM' => [6, 9, 10, 9, 9, 12, 124], 'ageF' => [6, 5, 21, 11, 1, 8, 195]],
            $idUluSoru => ['targetFam' => 91,  'ageM' => [5, 3, 9, 2, 3, 8, 145],   'ageF' => [5, 10, 4, 5, 4, 4, 148]],
        ];

        $maleFirstNames   = ['João','Francisco','António','Manuel','José','Afonso','Domingos','Carlos','Gabriel','Bernardo','Fernando','Mateus','Simão','Lucas','Marcos','Roberto','Lourenço','Vicente','Alberto','Daniel','Miguel','Pedro','Paulo','Estevão','Tomás','Jorge','Salvador','Abel','Adriano','Agostinho','Alexandre','Alfredo','Álvaro','Amílcar','Anacleto','André','Angelo','Aníbal','Arnaldo','Artur','Augusto','Aurélio','Bartolomeu','Basílio','Bento','Caetano','Camilo','Cândido','Celestino','César','Cipriano','Cláudio','Clemente','Constantino'];
        $femaleFirstNames = ['Maria','Ana','Rosa','Isabel','Filomena','Madalena','Teresa','Sofia','Rita','Clara','Angela','Francisca','Joana','Domingas','Cristina','Lucinda','Helena','Beatriz','Marta','Catarina','Jacinta','Amélia','Aurora','Carlota','Elisa','Glória','Albertina','Alda','Alice','Aliança','Alzira','Amália','Anabela','Antónia','Arlinda','Assunção','Augusta','Aura','Áurea','Bárbara','Belmira','Benedita','Benigna','Berta','Bibiana','Branca','Brizida'];
        $lastNames        = ['da Costa','dos Santos','da Silva','Rodrigues','Guterres','Soares','Pereira','de Araújo','Pires','Ribeiro','de Jesus','Amaral','Xavier','Belo','Alves','Cardozo','Mendonça','Pinto','Correia','Fonseca','Martins','Lopes','Cabral','Carvalho','Teixeira','Freitas','Barbosa','Barreto','Branco','Brito','Campos','Castro','Dias','Duarte','Fernandes','Ferreira','Gomes','Gonçalves'];
        $birthPlaces      = ['Laisorulai de Baixo','Baucau','Dili','Quelicai','Saua-Casa','Laga','Venilale','Viqueque','Lautem','Manatuto'];

        $pediduBatch = [];

        foreach ($aldeiaConfig as $aldeiaId => $cfg) {
            $neededFam = $cfg['targetFam'] - $realFam[$aldeiaId];
            if ($neededFam <= 0) continue;

            $slotsM = []; $slotsF = [];
            for ($g = 0; $g < 7; $g++) {
                for ($i = 0; $i < $cfg['ageM'][$g]; $i++) $slotsM[] = $g;
                for ($i = 0; $i < $cfg['ageF'][$g]; $i++) $slotsF[] = $g;
            }
            shuffle($slotsM); shuffle($slotsF);

            $adultMaleSlots    = array_values(array_filter($slotsM, fn($g) => $g >= 4));
            $nonAdultMaleSlots = array_values(array_filter($slotsM, fn($g) => $g < 4));
            
            $totalPeople = count($slotsM) + count($slotsF);
            $avgFamSize  = max(3, (int) round($totalPeople / $neededFam));

            $famKKs = [];
            for ($f = 0; $f < $neededFam; $f++) {
                $famKKs[] = ['numeru_kk' => $genKk(), 'id_aldeia' => $aldeiaId, 'created_at' => $now, 'updated_at' => $now];
            }
            $db->table('tabela_familia')->insertBatch($famKKs);

            $famIds = array_column(
                $db->table('tabela_familia')->where('id_aldeia', $aldeiaId)->orderBy('id_familia', 'ASC')->limit($neededFam)->offset($realFam[$aldeiaId])->get()->getResultArray(),
                'id_familia'
            );

            $popBatch = [];
            $aMIdx = 0; $naMIdx = 0; $fIdx = 0;
            $famIndex = 0; $memberCount = 0; $famIsOpen = false; $currentFamId = null;
            $xefePlaced = false; $konjugePlaced = false; $currentFamKbiit = null;

            $openNextFamily = function () use (&$famIndex, &$famIds, &$currentFamId, &$memberCount, &$xefePlaced, &$konjugePlaced, &$famIsOpen, &$currentFamKbiit, $genKbiit) {
                if ($famIndex < count($famIds)) {
                    $currentFamId = $famIds[$famIndex++];
                    $memberCount = 0; $xefePlaced = false; $konjugePlaced = false; $famIsOpen = true;
                    $currentFamKbiit = (mt_rand(1, 100) <= 15) ? $genKbiit() : null;
                } else { $famIsOpen = false; }
            };

            $openNextFamily();
            $totalSlots = count($adultMaleSlots) + count($nonAdultMaleSlots) + count($slotsF);
            $processed  = 0;

            while ($famIsOpen && $processed < $totalSlots) {
                $targetSize = mt_rand(3, min($avgFamSize + 1, 7));
                if ($famIndex >= count($famIds)) $targetSize = 9999;

                while ($memberCount < $targetSize && $famIsOpen) {
                    $gender = null; $ageGroup = null; $relasaun = 'Oan'; $statusKaza = 'Solteiru/a';

                    if (!$xefePlaced) {
                        if ($aMIdx < count($adultMaleSlots)) {
                            $gender = 'Mane'; $ageGroup = $adultMaleSlots[$aMIdx++]; $relasaun = 'Xefe Familia'; $statusKaza = 'Kazaradu/a'; $xefePlaced = true;
                        } elseif ($fIdx < count($slotsF)) {
                            $gender = 'Feto'; $ageGroup = $slotsF[$fIdx++]; $relasaun = 'Xefe Familia'; $statusKaza = ($ageGroup >= 4) ? 'Viuvu/a' : 'Solteiru/a'; $xefePlaced = true;
                        } else { break; }
                    } elseif (!$konjugePlaced && $xefePlaced && $fIdx < count($slotsF)) {
                        $candidateG = $slotsF[$fIdx];
                        if ($candidateG >= 4) {
                            $gender = 'Feto'; $ageGroup = $slotsF[$fIdx++]; $relasaun = 'Kônjuge'; $statusKaza = 'Kazaradu/a'; $konjugePlaced = true;
                        } else {
                            $gender = 'Feto'; $ageGroup = $slotsF[$fIdx++]; $relasaun = 'Oan'; $statusKaza = 'Solteiru/a';
                        }
                    } else {
                        $hasMale   = ($aMIdx < count($adultMaleSlots)) || ($naMIdx < count($nonAdultMaleSlots));
                        $hasFemale = ($fIdx < count($slotsF));
                        if ($hasMale && (!$hasFemale || mt_rand(0, 1))) {
                            $gender = 'Mane';
                            $ageGroup = ($naMIdx < count($nonAdultMaleSlots)) ? $nonAdultMaleSlots[$naMIdx++] : $adultMaleSlots[$aMIdx++];
                        } elseif ($hasFemale) {
                            $gender = 'Feto'; $ageGroup = $slotsF[$fIdx++];
                        } else { break; }
                        $relasaun = 'Oan'; $statusKaza = 'Solteiru/a';
                    }

                    if ($gender === null) break;

                    $yr = mt_rand($ageRanges[$ageGroup][0], $ageRanges[$ageGroup][1]);
                    $birthDate = sprintf('%d-%02d-%02d', $yr, mt_rand(1, 12), mt_rand(1, 28));
                    $age = 2026 - $yr;

                    $fullName = ($gender === 'Mane' ? $maleFirstNames[array_rand($maleFirstNames)] : $femaleFirstNames[array_rand($femaleFirstNames)]) . ' ' . $lastNames[array_rand($lastNames)];

                    // RELIGION (~0.8% Islam, ~14.2% Protestante, rest Katolika)
                    $relRand = mt_rand(1, 1000);
                    if ($relRand <= 8) { $idRelij = $rIslam; }
                    elseif ($relRand <= 150) { $idRelij = $rProtestante; }
                    else { $idRelij = $rKatolika; }

                    // PROFESSION (5% Veteranos for adults)
                    if ($age < 6) { $idProf = $pDesempregadu; }
                    elseif ($age < 18) { $idProf = $pEstudante; }
                    else {
                        if (mt_rand(1, 100) <= 5) { $idProf = $pVeteranos; }
                        else { $idProf = [$pAgrikultor, $pAgrikultor, $pNegosiante, $pFunsiPubliku, $pDesempregadu][mt_rand(0, 4)]; }
                    }

                    // EDUCATION (0.5% Mestradu for adults, 15% Pre Secundariu generally)
                    if ($age < 6) { $idLit = $lLaihaEskola; }
                    elseif ($age < 12) { $idLit = $lEnsinuBaziku; }
                    elseif ($age < 16) { 
                        // High chance for Pre Secundariu in this age bracket
                        $idLit = (mt_rand(1, 100) <= 80) ? $lPreSecundariu : $lEnsinuBaziku; 
                    }
                    else {
                        $litRand = mt_rand(1, 1000);
                        if ($litRand <= 5) { $idLit = $lMestradu; }       // 0.5%
                        elseif ($litRand <= 150) { $idLit = $lPreSecundariu; } // ~15%
                        elseif ($litRand <= 400) { $idLit = $lLisensiatura; } 
                        else { $idLit = $lSecundariu; }
                    }

                    // ELEITORAL (100% for age >= 17)
                    $noEleitoral = ($age >= 17) ? $genEleitoral() : null;

                    // ESTATUTU / PEDIDU (To generate some history in /admin/populasaun?type=estatutu)
                    $istadu = 'Moris';
                    $pediduRand = mt_rand(1, 1000);
                    if ($pediduRand <= 15) { // 1.5% Mate
                        $istadu = 'Mate';
                        $pediduBatch[] = [
                            'naran_pedidu' => 'Deklarasaun Mortalidade', 'pemohon' => $fullName,
                            'data_pedidu'  => sprintf('2026-%02d-%02d', mt_rand(1, 5), mt_rand(1, 28)),
                            'status'       => 'Aprovadu', 'id_aldeia' => $aldeiaId,
                            'meta_data'    => json_encode(['data_mate' => sprintf('2026-%02d-%02d', mt_rand(1, 5), mt_rand(1, 28)), 'kazu_mate' => 'Moras'])
                        ];
                    } elseif ($pediduRand <= 30) { // 1.5% Muda
                        $istadu = 'Muda';
                        $pediduBatch[] = [
                            'naran_pedidu' => 'Deklarasaun Muda Domisiliu', 'pemohon' => $fullName,
                            'data_pedidu'  => sprintf('2026-%02d-%02d', mt_rand(1, 5), mt_rand(1, 28)),
                            'status'       => 'Aprovadu', 'id_aldeia' => $aldeiaId,
                            'meta_data'    => null
                        ];
                    }

                    $popBatch[] = [
                        'nik' => $genNik(), 'no_eleitoral' => $noEleitoral, 'no_kbiit_laek' => $currentFamKbiit, 'naran_kompletu' => $fullName,
                        'fatin_moris' => $birthPlaces[array_rand($birthPlaces)], 'data_moris' => $birthDate,
                        'jeneru' => $gender, 'status_kaza' => $statusKaza, 'id_aldeia' => $aldeiaId,
                        'id_profisaun' => $idProf, 'id_relijiaun' => $idRelij, 'id_literatura' => $idLit,
                        'id_familia' => $currentFamId, 'relasaun_familia' => $relasaun, 'istadu' => $istadu,
                        'created_at' => $now, 'updated_at' => $now,
                    ];
                    $memberCount++; $processed++;
                }

                if ($famIndex < count($famIds)) {
                    $openNextFamily();
                } elseif ($famIsOpen) {
                    $remaining = ($aMIdx < count($adultMaleSlots)) || ($naMIdx < count($nonAdultMaleSlots)) || ($fIdx < count($slotsF));
                    if (!$remaining) $famIsOpen = false;
                } else { break; }
            }

            foreach (array_chunk($popBatch, 500) as $chunk) {
                $db->table('tabela_populasaun')->insertBatch($chunk);
            }
        }

        // Insert initial pedidus for Mate/Muda
        if (!empty($pediduBatch)) {
            $db->table('tabela_pedidu')->insertBatch($pediduBatch);
        }

        // =====================================================================
        // 8. GENERATE RICH PEDIDU HISTORY FOR EVERYONE
        // =====================================================================
        $allPop = $db->table('tabela_populasaun')->get()->getResultArray();
        $pediduBatch2 = [];
        
        $xefeMap = [];
        foreach ($allPop as $x) {
            if ($x['relasaun_familia'] === 'Xefe Familia') {
                $xefeMap[$x['id_familia']] = $x['naran_kompletu'];
            }
        }

        foreach ($allPop as $p) {
            // 0. Deklarasaun Nascimentu (For babies born in 2025-2026)
            $birthYear = (int) substr($p['data_moris'], 0, 4);
            if ($birthYear === 2025 || $birthYear === 2026) {
                $pemohon = $xefeMap[$p['id_familia']] ?? $p['naran_kompletu'];
                // Applied between 2 and 30 days after birth
                $dataPedidu = date('Y-m-d', strtotime($p['data_moris'] . ' + ' . mt_rand(2, 30) . ' days'));
                if ($dataPedidu > date('Y-m-d')) $dataPedidu = date('Y-m-d'); // ensure not in future if born today
                
                $pediduBatch2[] = [
                    'naran_pedidu' => 'Deklarasaun Nascimentu', 
                    'pemohon'      => $pemohon,
                    'data_pedidu'  => $dataPedidu,
                    'status'       => 'Aprovadu', 
                    'id_aldeia'    => $p['id_aldeia'],
                    'meta_data'    => json_encode([
                        'naran_labarik' => $p['naran_kompletu'],
                        'data_moris'    => $p['data_moris'],
                        'jeneru'        => $p['jeneru'],
                        'fatin_moris'   => $p['fatin_moris'] ?? 'Laisorulai de Baixo',
                    ]),
                    'created_at'   => $now, 'updated_at' => $now
                ];
            }

            // 1. Deklarasaun Eleitoral (If they have a card, they must have requested it)
            if (!empty($p['no_eleitoral'])) {
                $pediduBatch2[] = [
                    'naran_pedidu' => 'Deklarasaun Eleitoral', 
                    'pemohon'      => $p['naran_kompletu'],
                    'data_pedidu'  => sprintf('2025-%02d-%02d', mt_rand(1, 12), mt_rand(1, 28)),
                    'status'       => 'Aprovadu', 
                    'id_aldeia'    => $p['id_aldeia'],
                    'meta_data'    => json_encode(['tipu' => 'Foun', 'data_aprovasaun' => '2025-12-31']),
                    'created_at'   => $now, 'updated_at' => $now
                ];
                
                // Random chance they lost their card
                if (mt_rand(1, 100) <= 3) {
                    $pediduBatch2[] = [
                        'naran_pedidu' => 'Deklarasaun Eleitoral Lakon', 
                        'pemohon'      => $p['naran_kompletu'],
                        'data_pedidu'  => sprintf('2026-%02d-%02d', mt_rand(1, 5), mt_rand(1, 28)),
                        'status'       => ['Pendiente', 'Aprovadu'][mt_rand(0, 1)], 
                        'id_aldeia'    => $p['id_aldeia'],
                        'meta_data'    => null,
                        'created_at'   => $now, 'updated_at' => $now
                    ];
                }
            }
            
            // 2. Deklarasaun Kbiit Laek (For poor families, requested by Xefe Familia)
            if (!empty($p['no_kbiit_laek']) && $p['relasaun_familia'] === 'Xefe Familia') {
                $pediduBatch2[] = [
                    'naran_pedidu' => 'Deklarasaun Kbiit Laek', 
                    'pemohon'      => $p['naran_kompletu'],
                    'data_pedidu'  => sprintf('2026-%02d-%02d', mt_rand(1, 5), mt_rand(1, 28)),
                    'status'       => 'Aprovadu', 
                    'id_aldeia'    => $p['id_aldeia'],
                    'meta_data'    => null,
                    'created_at'   => $now, 'updated_at' => $now
                ];
            }
            
            // 3. Deklarasaun Bom Comportamentu (Often needed for jobs/school)
            // Desempregadu id is $pDesempregadu, which is 5. But let's just use 5.
            if ($p['id_profisaun'] != 5 && mt_rand(1, 100) <= 10) { 
                $pediduBatch2[] = [
                    'naran_pedidu' => 'Deklarasaun Bom Comportamentu', 
                    'pemohon'      => $p['naran_kompletu'],
                    'data_pedidu'  => sprintf('2026-%02d-%02d', mt_rand(1, 5), mt_rand(1, 28)),
                    'status'       => ['Pendiente', 'Aprovadu'][mt_rand(0, 1)], 
                    'id_aldeia'    => $p['id_aldeia'],
                    'meta_data'    => null,
                    'created_at'   => $now, 'updated_at' => $now
                ];
            }
        }

        // Insert in batches
        foreach (array_chunk($pediduBatch2, 500) as $chunk) {
            $db->table('tabela_pedidu')->insertBatch($chunk);
        }

        echo "Selesai! Baze dadus hetan atualiza ho dadus real kompletu (ho Eleitoral, Veteranos, Mestradu, no Pedidu Estatutu oin-oin).\n";
    }

    private function findId(array $map, array $variants, int $fallback): int
    {
        foreach ($variants as $v) {
            if (isset($map[strtolower($v)])) return $map[strtolower($v)];
        }
        return $fallback;
    }
    
    private function ensureMasterDataExists($db, $table, $column, $value)
    {
        $count = $db->table($table)->where($column, $value)->countAllResults();
        if ($count == 0) {
            $db->table($table)->insert([$column => $value]);
            echo "Aumenta dadus foun '$value' iha $table.\n";
        }
    }
}
