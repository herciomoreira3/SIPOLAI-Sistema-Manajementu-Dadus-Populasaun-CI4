<?php

namespace App\Models;

use CodeIgniter\Model;

class KartaSaiModel extends Model
{
    protected $table            = 'tabela_karta_sai';
    protected $primaryKey       = 'id_karta_sai';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['numeru_karta', 'destinatariu', 'asuntu', 'data_sai'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
