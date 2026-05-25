<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedDeklarasaunEleitoralTemplate extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $template = '<div style="font-family: \'Times New Roman\', Times, serif; color: #000; padding: 10px 30px; line-height: 1.6; font-size: 15px; background: #fff; width: 100%; box-sizing: border-box;">
    <!-- Cop/Header image -->
    <div style="text-align: center; margin-bottom: 25px;">
        <img src="[COP_IMAGE]" style="max-height: 140px; width: auto; max-width: 100%; display: block; margin: 0 auto;" alt="Header Cop">
    </div>
    
    <!-- Title -->
    <div style="text-align: center; font-weight: bold; font-size: 17px; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase;">
        DECLARASAUN ELEITORAL FOUN
    </div>
    
    <!-- OFÍCIO & REF -->
    <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 25px;">
        OFÍCIO<br>
        REF: [REF_NUMERU]
    </div>
    
    <!-- Body -->
    <div style="text-align: justify; margin-bottom: 15px;">
        Hau mak assina tuir mai ne’e:
    </div>
    <div style="margin-left: 30px; margin-bottom: 20px; line-height: 1.8;">
        <strong>Naran:</strong> Julio “Farkhus” Pinto<br>
        <strong>Pozisaun:</strong> Chefi Suco<br>
        <strong>Hela fatin:</strong> Saua-Casa/Laisorolai de Baixo
    </div>
    
    <div style="text-align: justify; margin-bottom: 15px;">
        Ha’u nu’udar chefe do suku Laisorulai de Baixo deklara katak ema ho naran mak hanesan tuir mai ne’e:
    </div>
    
    <div style="margin-left: 50px; margin-bottom: 25px; line-height: 1.8;">
        <strong>Naran:</strong> [NARAN_KOMPLETU]<br>
        <strong>Sexo:</strong> [SEXO]<br>
        <strong>Data de Nascimento:</strong> [DATA_MORIS]<br>
        <strong>Idade:</strong> [IDADE] Anos<br>
        <strong>Aldeia:</strong> [ALDEIA]<br>
        <strong>Suco:</strong> Laisorulai de Baixo<br>
        <strong>Posto Administrativo:</strong> Matebian<br>
        <strong>Municipio:</strong> Baucau
    </div>
    
    <div style="text-align: justify; margin-bottom: 20px; text-indent: 40px;">
        Sidadaun nebe identidade temi iha leten ne’e komunidade aldeia <strong>[ALDEIA]</strong> Suco Laisorolai de Baixo Posto Administrativo Matebian, Municipio Baucau sidadaun ne’e tebes duni seidauk iha Kartaun Eleitoral, tamba ne’e mak ha’u nu’udar chefe do Suku hato’o ba STAE Municipal de Baucau atu bele atende sidadaun ida ne’e tuir lei ne’ebé vigora iha Nasaun Repúblika Demokrátika de Timor – Leste.
    </div>
    
    <div style="text-align: justify; margin-bottom: 45px; text-indent: 40px;">
        Maka ne’e deit ami nian karta deklarasaun hato’o ba director STAE ita nia kolaborasaun ami lahaluha hato’o obrigado wain.
    </div>
    
    <!-- Signatures -->
    <table style="width: 100%; border: none; margin-top: 20px; font-size: 14px; font-family: \'Times New Roman\', Times, serif;">
        <tr>
            <td style="width: 50%; border: none; text-align: left; vertical-align: top; line-height: 1.5;">
                Visto Pelo<br>
                Posto Administrativo de Matebian<br>
                <br>
                <br>
                <br>
                <br>
                <strong>(Sr. Domingos Pereira L.E.d)</strong>
            </td>
            <td style="width: 50%; border: none; text-align: right; vertical-align: top; line-height: 1.5;">
                Laisorolai de Baixo, [DATA_AGORA]<br>
                Chefi Suco Laisorolai de Baixo<br>
                <br>
                <br>
                <br>
                <br>
                <strong>(Julio “Farkhus” Pinto)</strong>
            </td>
        </tr>
    </table>
    
    <!-- Bottom line footer info -->
    <div style="margin-top: 50px; border-top: 1px solid #000; padding-top: 5px; text-align: center; font-size: 11px; font-style: italic; color: #555;">
        Autoridade Municipio Baucau-Posto de Matebian-Suco Laisorolai de Baixo de Baixo- Timor –Leste. (+67078066526)
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
