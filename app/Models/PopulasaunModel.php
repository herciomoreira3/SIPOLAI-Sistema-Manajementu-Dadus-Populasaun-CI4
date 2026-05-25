<?php

namespace App\Models;

use CodeIgniter\Model;

class PopulasaunModel extends Model
{
    protected $table            = 'tabela_populasaun';
    protected $primaryKey       = 'id_populasaun';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nik', 'no_eleitoral', 'no_kbiit_laek', 'naran_kompletu', 'fatin_moris', 'data_moris', 'jeneru', 'status_kaza',
        'id_aldeia', 'id_profisaun', 'id_relijiaun', 'id_literatura',
        'id_familia', 'relasaun_familia', 'istadu'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
