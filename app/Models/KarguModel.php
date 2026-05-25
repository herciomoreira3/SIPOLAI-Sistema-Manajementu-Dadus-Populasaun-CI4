<?php

namespace App\Models;

use CodeIgniter\Model;

class KarguModel extends Model
{
    protected $table            = 'tabela_kargu';
    protected $primaryKey       = 'id_kargu';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['naran_kargu'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
