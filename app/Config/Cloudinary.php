<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cloudinary extends BaseConfig
{
    public $cloudName;
    public $apiKey;
    public $apiSecret;

    public function __construct()
    {
        $this->cloudName = getenv('CLOUDINARY_CLOUD_NAME');
        $this->apiKey = getenv('CLOUDINARY_API_KEY');
        $this->apiSecret = getenv('CLOUDINARY_API_SECRET');
    }
}
