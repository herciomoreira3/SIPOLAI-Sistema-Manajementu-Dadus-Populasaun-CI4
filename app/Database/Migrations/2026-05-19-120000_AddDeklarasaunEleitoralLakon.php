<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeklarasaunEleitoralLakon extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Insert Deklarasaun Eleitoral Lakon into tabela_tipu_pedidu if it doesn\'t exist
        $checkType = $db->table('tabela_tipu_pedidu')->where('naran_tipu_pedidu', 'Deklarasaun Eleitoral Lakon')->get()->getRow();
        if (!$checkType) {
            $db->table('tabela_tipu_pedidu')->insert([
                'naran_tipu_pedidu' => 'Deklarasaun Eleitoral Lakon',
                'template_formatu'  => '',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s')
            ]);
        }

        // 2. Define beautiful HTML template for Deklarasaun Eleitoral Lakon (Lost Electoral Card Certificate)
        $eleitoralLakonTemplate = '<div style="font-family: \'Times New Roman\', Times, serif; color: #000; padding: 10px 30px; line-height: 1.6; font-size: 15px; background: #fff; width: 100%; box-sizing: border-box;">
    <!-- Cop/Header image -->
    <div style="text-align: center; margin-bottom: 25px;">
        <img src="[COP_IMAGE]" style="max-height: 140px; width: auto; max-width: 100%; display: block; margin: 0 auto;" alt="Header Cop">
    </div>
    
    <!-- Title -->
    <div style="text-align: center; font-weight: bold; font-size: 17px; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase;">
        DEKLARASAUN ELEITORAL LAKON (PERDA DE CARTÃO ELEITORAL)
    </div>
    
    <!-- OFÍCIO & REF -->
    <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 25px;">
        OFÍCIO<br>
        No.Ref: [REF_NUMERU]
    </div>
    
    <!-- Body -->
    <div style="text-align: justify; margin-bottom: 15px; text-indent: 40px;">
        Eu abaixo assinado, <strong>Júlio “Farkhus” Pinto</strong>, Chefe do Suco Laisorolai de Baixo, Posto Administrativo Matebian, Município Baucau, deklara ho responsabilidade tomak katak sidadaun ne’ebé naran temi iha okos ne\'e lakon duni nia <strong>Kartaun Eleitoral</strong> no presiza deklarasaun ne\'e hodi bele husu fali kartaun foun:
    </div>
    
    <div style="margin-left: 50px; margin-bottom: 25px; line-height: 1.8;">
        <strong>Naran Kompletu:</strong> [NARAN_KOMPLETU]<br>
        <strong>Sexo:</strong> [SEXO]<br>
        <strong>Data de Nascimento:</strong> [DATA_MORIS]<br>
        <strong>Idade:</strong> [IDADE] Anos<br>
        <strong>No. Cart. Eleitoral (Lakon):</strong> [NIK]<br>
        <strong>Aldeia:</strong> [ALDEIA]<br>
        <strong>Residénsia:</strong> Suco Laisorolai de Baixo
    </div>
    
    <div style="text-align: justify; margin-bottom: 20px; text-indent: 40px;">
        Sidadaun refere relata katak nia Kartaun Eleitoral lakon duni tanba kazu ruma ne\'ebé la provizoria. Tanba ne\'e ami fó deklarasaun ne\'e atu nune\'e bele lori ba STAE (Secretariado Técnico de Administração Eleitoral) hodi trata fali Kartaun Eleitoral foun (segunda via).
    </div>
    
    <div style="text-align: justify; margin-bottom: 40px; text-indent: 40px;">
        Ba verdade hotu, ami fó sai deklarasaun lakon ne\'e, hodi bele uza ho didiak ba interese sidadaun nian.
    </div>
    
    <!-- Signatures -->
    <table style="width: 100%; border: none; margin-top: 20px; font-size: 14px; font-family: \'Times New Roman\', Times, serif;">
        <tr>
            <td style="width: 50%; border: none; text-align: left; vertical-align: top; line-height: 1.5;">
                Visto husi:<br>
                Chefe do Posto Administrativo de Matebian<br>
                <br>
                <br>
                <br>
                <br>
                <strong>(Sr. Domingos Pereira L.E.d)</strong>
            </td>
            <td style="width: 50%; border: none; text-align: right; vertical-align: top; line-height: 1.5;">
                Laisorolai de Baixo, [DATA_AGORA]<br>
                Chefe do Suco Laisorolai de Baixo<br>
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
        Autoridade Lokal Suco Laisorolai de Baixo-Posto Matebian-Municipal de Baucau-Timor Leste (78066526)
    </div>
</div>';

        // 3. Update template in database
        $db->table('tabela_tipu_pedidu')
           ->where('naran_tipu_pedidu', 'Deklarasaun Eleitoral Lakon')
           ->update(['template_formatu' => $eleitoralLakonTemplate]);

        // 4. Create new submenus under "Inventoriu Deklarasaun"
        $inventoriuParent = $db->table('menu')->where('title', 'Inventoriu Deklarasaun')->get()->getRow();
        if ($inventoriuParent) {
            $child = [
                'parent_id'  => $inventoriuParent->id,
                'title'      => 'Deklarasaun Eleitoral Lakon',
                'icon'       => 'fas fa-id-badge',
                'route'      => 'admin/pedidu?naran_pedidu=Deklarasaun+Eleitoral+Lakon',
                'sequence'   => 6,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Check if already exists
            $check = $db->table('menu')->where(['title' => $child['title'], 'parent_id' => $child['parent_id']])->get()->getRow();
            if (!$check) {
                $db->table('menu')->insert($child);
                $childId = $db->insertID();

                // Map to specified groups (admin, xefe-suku, sekretaria)
                $groups = $db->table('auth_groups')->whereIn('name', ['admin', 'xefe-suku', 'sekretaria'])->get()->getResult();
                foreach ($groups as $grp) {
                    $db->table('groups_menu')->insert([
                        'group_id' => $grp->id,
                        'menu_id'  => $childId,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Remove menu mapping
        $inventoriuParent = $db->table('menu')->where('title', 'Inventoriu Deklarasaun')->get()->getRow();
        if ($inventoriuParent) {
            $menuItem = $db->table('menu')->where(['title' => 'Deklarasaun Eleitoral Lakon', 'parent_id' => $inventoriuParent->id])->get()->getRow();
            if ($menuItem) {
                $db->table('groups_menu')->where('menu_id', $menuItem->id)->delete();
                $db->table('menu')->where('id', $menuItem->id)->delete();
            }
        }

        // Delete type
        $db->table('tabela_tipu_pedidu')->where('naran_tipu_pedidu', 'Deklarasaun Eleitoral Lakon')->delete();
    }
}
