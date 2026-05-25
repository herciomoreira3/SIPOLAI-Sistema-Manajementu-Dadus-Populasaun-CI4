<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfisaunModel extends Model
{
    protected $table            = 'tabela_profisaun';
    protected $primaryKey       = 'id_profisaun';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['naran_profisaun'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
