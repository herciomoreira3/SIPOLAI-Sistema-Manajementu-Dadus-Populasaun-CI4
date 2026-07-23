
&lt;?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoToEstruturaSuku extends Migration
{
    public function up()
    {
        $this-&gt;forge-&gt;addColumn('tabela_estrutura_suku', [
            'foto' =&gt; [
                'type'       =&gt; 'VARCHAR',
                'constraint' =&gt; '255',
                'null'       =&gt; true,
            ],
        ]);
    }

    public function down()
    {
        $this-&gt;forge-&gt;dropColumn('tabela_estrutura_suku', 'foto');
    }
}
