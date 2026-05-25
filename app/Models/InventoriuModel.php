<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoriuModel extends Model
{
    protected $table            = 'tabela_inventoriu';
    protected $primaryKey       = 'id_inventoriu';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pedidu', 'naran_kompletu', 'jeneru', 'data_moris', 'fatin_moris',
        'naran_aldeia', 'nik', 'no_eleitoral', 'no_kbiit_laek', 'meta_data'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
