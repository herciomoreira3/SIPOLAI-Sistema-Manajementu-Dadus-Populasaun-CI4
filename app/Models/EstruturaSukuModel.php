<?php

namespace App\Models;

use CodeIgniter\Model;

class EstruturaSukuModel extends Model
{
    protected $table            = 'tabela_estrutura_suku';
    protected $primaryKey       = 'id_estrutura';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_populasaun',
        'id_aldeia',
        'naran_membru',
        'kargu',
        'periodo_hahula',
        'periodo_remata',
        'status_kargu',
        'foto'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get structure members with their resident data
     */
    public function getEstrutura()
    {
        return $this->select('tabela_estrutura_suku.*, tabela_populasaun.nik, tabela_populasaun.jeneru, tabela_aldeia.naran_aldeia')
                    ->join('tabela_populasaun', 'tabela_populasaun.id_populasaun = tabela_estrutura_suku.id_populasaun', 'left')
                    ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_estrutura_suku.id_aldeia', 'left')
                    ->findAll();
    }
}
