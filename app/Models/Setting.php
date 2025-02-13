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

namespace App\Models;

class Setting extends BaseModel
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'lastmod_datetime',
        'lastmod_user_id',
        'lastmod_username',
    ];

    static $settings = [];
    static $is_initialized = false;

    private static function _init()
    {
        if (!static::$is_initialized) {
            $items = Setting::all();
            foreach ($items as $item) {
                static::$settings[$item->key] = $item->value;
            }
            static::$is_initialized = true;
        }
    }

    public static function value($key, $default = null)
    {
        static::_init();
        return isset(static::$settings[$key]) ? static::$settings[$key] : $default;
    }

    public static function values()
    {
        static::_init();
        return static::$settings;
    }

    public static function setValue($key, $value)
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        static::$settings[$key] = $value;
    }
}
