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
        $cloudinaryUrl = getenv('CLOUDINARY_URL');
        if (!empty($cloudinaryUrl)) {
            // Parse CLOUDINARY_URL: cloudinary://API_KEY:API_SECRET@CLOUD_NAME
            $parsed = parse_url($cloudinaryUrl);
            if (isset($parsed['user'], $parsed['pass'], $parsed['host'])) {
                $this->apiKey = $parsed['user'];
                $this->apiSecret = $parsed['pass'];
                $this->cloudName = $parsed['host'];
            }
        } else {
            $this->cloudName = getenv('CLOUDINARY_CLOUD_NAME');
            $this->apiKey = getenv('CLOUDINARY_API_KEY');
            $this->apiSecret = getenv('CLOUDINARY_API_SECRET');
        }
    }
}
