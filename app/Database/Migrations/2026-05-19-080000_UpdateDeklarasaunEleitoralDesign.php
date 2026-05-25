<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateDeklarasaunEleitoralDesign extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $template = '<div style="font-family: \'Times New Roman\', Times, serif; color: #000; padding: 25px 35px; line-height: 1.45; font-size: 13.5px; background: #fff; width: 100%; box-sizing: border-box; max-width: 800px; margin: 0 auto;">

    <!-- Header dengan Bottom Line -->
    <div style="text-align: center; margin-bottom: 20px; border-bottom: 2.5px solid #000; padding-bottom: 8px;">
        <h6 style="font-size: 15px; font-weight: bold; margin: 5px 0; text-transform: uppercase;">
            SUCO DE LAISOROLAI DE BAIXO - MATEBIAN - BAUCAU
        </h6>
    </div>

    <!-- Title -->
    <div style="text-align: center; margin-bottom: 18px;">
        <h6 style="font-size: 17px; font-weight: bold; text-decoration: underline; margin: 8px 0; text-transform: uppercase;">
            DECLARASAUN ELEITORAL FOUN
        </h6>
    </div>

    <!-- Ofício -->
    <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 20px;">
        OFICIO<br>
        REF: [REF_NUMERU]
    </div>

    <!-- Isi Surat -->
    <div style="text-align: justify; margin-bottom: 12px;">
        Ha\'u mak asina tuir mai ne\'e:
    </div>

    <div style="margin-left: 25px; margin-bottom: 15px; line-height: 1.6;">
        <strong>Naran:</strong> Julio “Farkhus” Pinto<br>
        <strong>Pozisaun:</strong> Chefi Suco<br>
        <strong>Hela fatin:</strong> Saua-Casa / Laisorolai de Baixo
    </div>

    <div style="text-align: justify; margin-bottom: 12px;">
        Ha’u nu’udar Chefe do Suku Laisorolai de Baixo deklara katak ema ho naran mak hanesan tuir mai ne’e:
    </div>

    <!-- Tabel Data -->
    <table style="width: 100%; margin: 12px 0; border-collapse: collapse; font-size: 13.5px;">
        <tbody><tr><td style="width: 32%; padding: 3px 0;"><strong>Naran</strong></td><td style="width: 3%;">:</td><td>[NARAN_KOMPLETU]</td></tr>
        <tr><td style="padding: 3px 0;"><strong>Sexo</strong></td><td>:</td><td>[SEXO]</td></tr>
        <tr><td style="padding: 3px 0;"><strong>Data de Nascimento</strong></td><td>:</td><td>[DATA_MORIS]</td></tr>
        <tr><td style="padding: 3px 0;"><strong>Idade</strong></td><td>:</td><td>[IDADE] Anos</td></tr>
        <tr><td style="padding: 3px 0;"><strong>Aldeia</strong></td><td>:</td><td>[ALDEIA]</td></tr>
        <tr><td style="padding: 3px 0;"><strong>Suco</strong></td><td>:</td><td>Laisorolai de Baixo</td></tr>
        <tr><td style="padding: 3px 0;"><strong>Posto Administrativo</strong></td><td>:</td><td>Matebian</td></tr>
        <tr><td style="padding: 3px 0;"><strong>Municipio</strong></td><td>:</td><td>Baucau</td></tr>
    </tbody></table>

    <div style="text-align: justify; line-height: 1.5; text-indent: 30px; margin-bottom: 30px;">
        Sidadaun ne’ebé identidade iha leten ne’e komunidade aldeia <strong>[ALDEIA]</strong>, Suco Laisorolai de Baixo, Posto Administrativo Matebian, Munisípiu Baucau, sidadaun ne’e seidauk iha Kartaun Eleitoral.<br><br>
        Tambá ne’e mak ha’u nu’udar Chefe do Suku hato’o ba STAE Municipal de Baucau atu bele atende sidadaun ida ne’e tuir lei ne’ebé vigora iha Repúblika Demokrátika de Timor-Leste.<br><br>
        Maka ne’e deit ami nian karta deklarasaun hato’o ba Diretor STAE nia kolaborasaun. Ami la haluha hato’o obrigado wain.
    </div>

    <!-- Tanda Tangan dengan Gambar -->
    <table style="width: 100%; margin-top: 40px; font-size: 13.5px;">
        <tbody><tr>
            <!-- Kolom Kiri: Visto Pelo -->
            <td style="width: 50%; vertical-align: top; text-align: left;">
                Visto Pelo<br>
                Posto Administrativo de Matebian<br><br>
                
                <!-- Gambar Tanda Tangan Visto -->
                <img src="https://your-link-tanda-tangan-visto.jpg" alt="Tanda Tangan Visto" style="max-width: 170px; max-height: 75px; margin: 8px 0;">
                
                <br>
                <strong>(Sr. Domingos Pereira L.E.d)</strong>
            </td>

            <!-- Kolom Kanan: Chefe Suco -->
            <td style="width: 50%; vertical-align: top; text-align: right;">
                Laisorolai de Baixo, [DATA_AGORA]<br>
                Chefi Suco Laisorolai de Baixo<br><br>
                
                <!-- Gambar Tanda Tangan Chefe Suco -->
                <img src="https://your-link-tanda-tangan-chefe.jpg" alt="Tanda Tangan" style="max-width: 170px; max-height: 75px; margin: 8px 0;">
                
                <br>
                <strong>(Julio “Farkhus” Pinto)</strong>
            </td>
        </tr>
    </tbody></table>

    <!-- Footer -->
    <div style="margin-top: 45px; padding-top: 8px; border-top: 1px solid #000; text-align: center; font-size: 11px; color: #333;">
        Autoridade Municipio Baucau - Posto de Matebian - Suco Laisorolai de Baixo<br>
        Timor-Leste. (+670 7806 6526)
    </div>

</div>';

        $db->table('tabela_tipu_pedidu')
           ->where('naran_tipu_pedidu', 'Deklarasaun Eleitoral')
           ->update(['template_formatu' => $template]);
    }

    public function down()
    {
        // No down needed
    }
}
