<?php

namespace App\Models;

use CodeIgniter\Model;

class FamiliaModel extends Model
{
    protected $table            = 'tabela_familia';
    protected $primaryKey       = 'id_familia';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['numeru_kk', 'id_aldeia', 'foto'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
