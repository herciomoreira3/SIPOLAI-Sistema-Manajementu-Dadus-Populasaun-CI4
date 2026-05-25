<?php

namespace App\Models;

use CodeIgniter\Model;

class PediduModel extends Model
{
    protected $table            = 'tabela_pedidu';
    protected $primaryKey       = 'id_pedidu';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'naran_pedidu', 'pemohon', 'data_pedidu', 'status', 'id_aldeia', 'meta_data'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
