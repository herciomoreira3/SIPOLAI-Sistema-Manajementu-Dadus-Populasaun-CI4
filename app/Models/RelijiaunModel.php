<?php

namespace App\Models;

use CodeIgniter\Model;

class RelijiaunModel extends Model
{
    protected $table            = 'tabela_relijiaun';
    protected $primaryKey       = 'id_relijiaun';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['naran_relijiaun'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
