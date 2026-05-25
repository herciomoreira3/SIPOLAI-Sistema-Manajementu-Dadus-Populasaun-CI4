<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeklarasaunBomComportamento extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Insert Deklarasaun Bom Comportamento into tabela_tipu_pedidu if it doesn't exist
        $checkType = $db->table('tabela_tipu_pedidu')->where('naran_tipu_pedidu', 'Deklarasaun Bom Comportamento')->get()->getRow();
        if (!$checkType) {
            $db->table('tabela_tipu_pedidu')->insert([
                'naran_tipu_pedidu' => 'Deklarasaun Bom Comportamento',
                'template_formatu'  => '',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s')
            ]);
        }

        // 2. Define beautiful HTML templates
        $bomComportamentoTemplate = '<div style="font-family: \'Times New Roman\', Times, serif; color: #000; padding: 10px 30px; line-height: 1.6; font-size: 15px; background: #fff; width: 100%; box-sizing: border-box;">
    <!-- Cop/Header image -->
    <div style="text-align: center; margin-bottom: 25px;">
        <img src="[COP_IMAGE]" style="max-height: 140px; width: auto; max-width: 100%; display: block; margin: 0 auto;" alt="Header Cop">
    </div>
    
    <!-- Title -->
    <div style="text-align: center; font-weight: bold; font-size: 17px; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase;">
        ATESTADO DO BOM COMPORTAMENTO
    </div>
    
    <!-- OFÍCIO & REF -->
    <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 25px;">
        OFÍCIO<br>
        REF: [REF_NUMERU]
    </div>
    
    <!-- Body -->
    <div style="text-align: justify; margin-bottom: 15px; text-indent: 40px;">
        Eu abaixo assinado, <strong>Júlio “Farkhus” Pinto</strong>, como Chefe do Suco Laisorolai de Baixo, residente em Aldeia Saua-Uasa, Posto Administrativo Matebian, Município Baucau, testifico com sinceridade o indivíduo de nome abaixo mencionado:
    </div>
    
    <div style="margin-left: 50px; margin-bottom: 25px; line-height: 1.8;">
        <strong>Nome:</strong> [NARAN_KOMPLETU]<br>
        <strong>Local e Data de Nascimento:</strong> [DATA_MORIS]<br>
        <strong>No. Cart. Eleitoral:</strong> [NIK]<br>
        <strong>Sexo:</strong> [SEXO]<br>
        <strong>Aldeia:</strong> [ALDEIA]<br>
        <strong>Residência Atual:</strong> [ALDEIA] / Laisorolai de Baixo
    </div>
    
    <div style="text-align: justify; margin-bottom: 20px; text-indent: 40px;">
        Durante a minha carreira como Chefe do Suco Laisorolai de Baixo, verifiquei que este indivíduo nunca foi suspeito ou considerado delinquente criminal na Polícia ou na Instituição Tradicional. Com efeito, declaro que este indivíduo é digno de obter este <strong>ATESTADO DO BOM COMPORTAMENTO</strong>, para fins de prefazer-lhe os seus documentos necessários.
    </div>
    
    <div style="text-align: justify; margin-bottom: 45px; text-indent: 40px;">
        Mais declaro, que caso houver qualquer contradição a respeito deste atestado, estou decidido a responder pelo ato judicial perante a lei em vigor. Por verdade, lavro o presente ATESTADO DO BOM COMPORTAMENTO, que por mim será assinado com o carimbo em uso nesta Secretaria do Suco de Laisorolai de Baixo.
    </div>
    
    <!-- Signatures -->
    <table style="width: 100%; border: none; margin-top: 20px; font-size: 14px; font-family: \'Times New Roman\', Times, serif;">
        <tr>
            <td style="width: 50%; border: none; text-align: left; vertical-align: top; line-height: 1.5;">
                Homologado por:<br>
                Administrador do Posto Administrativo de Matebian<br>
                <br>
                <br>
                <br>
                <br>
                <strong>(Sr. Domingos Pereira)</strong>
            </td>
            <td style="width: 50%; border: none; text-align: right; vertical-align: top; line-height: 1.5;">
                Laisorolai de Baixo, [DATA_AGORA]<br>
                Chefe do Suco Laisorolai de Baixo<br>
                <br>
                <br>
                <br>
                <br>
                <strong>(Sr. Júlio “Farkhus” Pinto)</strong>
            </td>
        </tr>
    </table>
    
    <!-- PNTL Visto Section -->
    <div style="margin-top: 40px; text-align: center; line-height: 1.5; font-size: 14px;">
        Visto Pelo:<br>
        Comandante PNTL Esquadra Posto Adm Quelicai<br>
        <br>
        <br>
        <br>
        <strong>(Justino do Carmo Fernandes)</strong><br>
        Insp. da Polícia ID# 11352
    </div>
    
    <!-- Bottom line footer info -->
    <div style="margin-top: 40px; border-top: 1px solid #000; padding-top: 5px; text-align: center; font-size: 11px; font-style: italic; color: #555;">
        Autoridade Local Suco Laisorolai de Baixo-Posto Matebian-Municipal de Baucau-Timor Leste (78066526)
    </div>
</div>';

        $nascimentuTemplate = '<div style="font-family: \'Times New Roman\', Times, serif; color: #000; padding: 10px 30px; line-height: 1.6; font-size: 15px; background: #fff; width: 100%; box-sizing: border-box;">
    <!-- Cop/Header image -->
    <div style="text-align: center; margin-bottom: 25px;">
        <img src="[COP_IMAGE]" style="max-height: 140px; width: auto; max-width: 100%; display: block; margin: 0 auto;" alt="Header Cop">
    </div>
    
    <!-- Title -->
    <div style="text-align: center; font-weight: bold; font-size: 17px; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase;">
        DEKLARASAUN NASIMENTU
    </div>
    
    <!-- OFÍCIO & REF -->
    <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 25px;">
        OFÍCIO<br>
        No.Ref: [REF_NUMERU]
    </div>
    
    <!-- Body -->
    <div style="text-align: justify; margin-bottom: 15px;">
        Ha’u mak asina no deklara, ho identidade hanesan tuir mai ne’e:
    </div>
    <div style="margin-left: 30px; margin-bottom: 20px; line-height: 1.8;">
        <strong>Naran:</strong> Júlio “Farkhus” Pinto<br>
        <strong>Pozisaun:</strong> Chefe Suco Laisorolai de Baixo<br>
        <strong>Hela fatin:</strong> Saua-Casa/Laisorolai de Baixo
    </div>
    
    <div style="text-align: justify; margin-bottom: 15px;">
        Deklara katak tuir investigasaun husi ami família, ba sidadaun ida ho identidade hanesan tuir mai ne’e:
    </div>
    
    <div style="margin-left: 50px; margin-bottom: 25px; line-height: 1.8;">
        <strong>Naran Kompletu:</strong> [NARAN_KOMPLETU]<br>
        <strong>Sexo:</strong> [SEXO]<br>
        <strong>Data de Nascimento:</strong> [DATA_MORIS]<br>
        <strong>Idade:</strong> [IDADE] Anos<br>
        <strong>Aldeia:</strong> [ALDEIA]<br>
        <strong>Suco:</strong> Laisorolai de Baixo<br>
        <strong>Posto Administrativo:</strong> Matebian<br>
        <strong>Municipio:</strong> Baucau
    </div>
    
    <div style="text-align: justify; margin-bottom: 20px; text-indent: 40px;">
        Sidadaun ne’ebé identidade temi iha leten ne’e tebes duni moris iha Suco Laisorolai de Baixo, Posto Administrativo Matebian, Municipio Baucau. Ne’e mak deklarasaun husi ami, se karik iha loron ikus maka dadus no informasaun hirak ne’e la lo’os maka ami prontu atu hatan iha Tribunál tuir Lei ne’ebé vigór iha Nasaun Repúblika Demokrátika de Timor-Leste.
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

        $mortalidadeTemplate = '<div style="font-family: \'Times New Roman\', Times, serif; color: #000; padding: 10px 30px; line-height: 1.6; font-size: 15px; background: #fff; width: 100%; box-sizing: border-box;">
    <!-- Cop/Header image -->
    <div style="text-align: center; margin-bottom: 25px;">
        <img src="[COP_IMAGE]" style="max-height: 140px; width: auto; max-width: 100%; display: block; margin: 0 auto;" alt="Header Cop">
    </div>
    
    <!-- Title -->
    <div style="text-align: center; font-weight: bold; font-size: 17px; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase;">
        DEKLARASAUN MORTALIDADE (MATE)
    </div>
    
    <!-- OFÍCIO & REF -->
    <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 25px;">
        OFÍCIO<br>
        No.Ref: [REF_NUMERU]
    </div>
    
    <!-- Body -->
    <div style="text-align: justify; margin-bottom: 15px;">
        Ha’u mak asina no deklara, ho identidade hanesan tuir mai ne’e:
    </div>
    <div style="margin-left: 30px; margin-bottom: 20px; line-height: 1.8;">
        <strong>Naran:</strong> Júlio “Farkhus” Pinto<br>
        <strong>Pozisaun:</strong> Chefe Suco Laisorolai de Baixo<br>
        <strong>Hela fatin:</strong> Saua-Casa/Laisorolai de Baixo
    </div>
    
    <div style="text-align: justify; margin-bottom: 15px;">
        Deklara ho responsabilidade tomak katak sidadaun ne’ebé temi naran iha leten ne’e tebes duni mate/falecido ona:
    </div>
    
    <div style="margin-left: 50px; margin-bottom: 25px; line-height: 1.8;">
        <strong>Naran Kompletu:</strong> [NARAN_KOMPLETU]<br>
        <strong>Sexo:</strong> [SEXO]<br>
        <strong>Data de Nascimento:</strong> [DATA_MORIS]<br>
        <strong>Idade:</strong> [IDADE] Anos<br>
        <strong>Aldeia:</strong> [ALDEIA]<br>
        <strong>Suco:</strong> Laisorolai de Baixo<br>
        <strong>Posto Administrativo:</strong> Matebian<br>
        <strong>Municipio:</strong> Baucau
    </div>
    
    <div style="text-align: justify; margin-bottom: 20px; text-indent: 40px;">
        Sidadaun ne’ebé identidade temi iha leten ne’e tebes duni moris no mate iha Suco Laisorolai de Baixo, Posto Administrativo Matebian, Municipio Baucau. Ne’e mak deklarasaun husi ami, se karik iha loron ikus maka dadus no informasaun hirak ne’e la lo’os maka ami prontu atu hatan iha Tribunál tuir Lei ne’ebé vigór iha Nasaun Repúblika Demokrátika de Timor-Leste.
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

        // 3. Update templates in database
        $db->table('tabela_tipu_pedidu')
           ->where('naran_tipu_pedidu', 'Deklarasaun Bom Comportamento')
           ->update(['template_formatu' => $bomComportamentoTemplate]);

        $db->table('tabela_tipu_pedidu')
           ->where('naran_tipu_pedidu', 'Deklarasaun Nascimentu')
           ->update(['template_formatu' => $nascimentuTemplate]);

        $db->table('tabela_tipu_pedidu')
           ->where('naran_tipu_pedidu', 'Deklarasaun Mortalidade')
           ->update(['template_formatu' => $mortalidadeTemplate]);

        // 4. Create new submenus under "Inventoriu Deklarasaun"
        $inventoriuParent = $db->table('menu')->where('title', 'Inventoriu Deklarasaun')->get()->getRow();
        if ($inventoriuParent) {
            $child = [
                'parent_id'  => $inventoriuParent->id,
                'title'      => 'Deklarasaun Bom Comportamento',
                'icon'       => 'fas fa-gavel',
                'route'      => 'admin/pedidu?naran_pedidu=Deklarasaun+Bom+Comportamento',
                'sequence'   => 4,
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
            $menuItem = $db->table('menu')->where(['title' => 'Deklarasaun Bom Comportamento', 'parent_id' => $inventoriuParent->id])->get()->getRow();
            if ($menuItem) {
                $db->table('groups_menu')->where('menu_id', $menuItem->id)->delete();
                $db->table('menu')->where('id', $menuItem->id)->delete();
            }
        }

        // Delete type
        $db->table('tabela_tipu_pedidu')->where('naran_tipu_pedidu', 'Deklarasaun Bom Comportamento')->delete();
    }
}
