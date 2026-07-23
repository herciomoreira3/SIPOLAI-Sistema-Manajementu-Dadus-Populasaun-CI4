<?php

if (!function_exists('get_photo_url')) {
    function get_photo_url($foto, $folder = 'familia')
    {
        if (empty($foto)) {
            return null;
        }

        if (filter_var($foto, FILTER_VALIDATE_URL)) {
            return $foto;
        }

        $localPath = FCPATH . 'uploads/' . $folder . '/' . $foto;
        if (file_exists($localPath)) {
            return base_url('uploads/' . $folder . '/' . $foto);
        }

        return null;
    }
}
