<?php

namespace App\Models;

use CodeIgniter\Model;

class KartaTamaModel extends Model
{
    protected $table            = 'tabela_karta_tama';
    protected $primaryKey       = 'id_karta_tama';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['numeru_karta', 'emitente', 'asuntu', 'data_tama'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
