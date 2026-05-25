<?php

namespace App\Models;

use CodeIgniter\Model;

class LiteraturaModel extends Model
{
    protected $table            = 'tabela_literatura';
    protected $primaryKey       = 'id_literatura';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['naran_literatura'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
