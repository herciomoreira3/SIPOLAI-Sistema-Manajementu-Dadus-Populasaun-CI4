<?php

namespace App\Models;

use CodeIgniter\Model;

class TipuPediduModel extends Model
{
    protected $table            = 'tabela_tipu_pedidu';
    protected $primaryKey       = 'id_tipu_pedidu';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['naran_tipu_pedidu', 'template_formatu'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
