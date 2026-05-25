<?php

namespace App\Models;

use CodeIgniter\Model;

class FormatuRelatoriuModel extends Model
{
    protected $table            = 'tabela_formatu_relatoriu';
    protected $primaryKey       = 'id_formatu_relatoriu';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'naran_relatoriu',
        'template_cop',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
