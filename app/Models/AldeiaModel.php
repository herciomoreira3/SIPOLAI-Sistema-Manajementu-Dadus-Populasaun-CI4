<?php

namespace App\Models;

use CodeIgniter\Model;

class AldeiaModel extends Model
{
    protected $table            = 'tabela_aldeia';
    protected $primaryKey       = 'id_aldeia';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['naran_aldeia', 'id_suku'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
