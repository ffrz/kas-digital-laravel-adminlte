<?php
/**
 * Hak Cipta (C) 2025 Fahmi Fauzi Rahman
 * Seluruh Hak Cipta Dilindungi Undang-Undang.
 *
 * File ini bersifat rahasia dan merupakan hak milik eksklusif.
 * Dilarang keras menjual kembali aplikasi ini kepada pihak lain
 * dalam bentuk apapun.
 *
 * Penulis  : Fahmi Fauzi Rahman
 * Kontak   : fahmifauzirahman@gmail.com
 * Youtube  : https://www.youtube.com/@hobi_coding
 * Facebook : https://www.facebook.com/fahmifauzirahman
 * Instagram: https://www.instagram.com/fahmi.fauzi.rahman
 * Tiktok   : https://www.tiktok.com/@ffr__85
 * Lisensi  : Hak Milik (Proprietary)
 */

namespace App\Http\Controllers;

abstract class Controller
{
    //
    protected $base_url = '';
    protected $view_path = '';

    protected function redirect($url = '') {
        return redirect(url($this->base_url . '/' . $url));
    }

    protected function view($file, $data = [], $merge = []) {
        $path = $this->base_url;

        if (!empty($this->view_path))
            $path = $this->view_path;

        $path = $path . '/' . $file;

        return view($path, $data, $merge);
    }
}
