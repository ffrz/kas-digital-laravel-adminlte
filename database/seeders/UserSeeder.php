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

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            'username' => 'admin',
            'password' => Hash::make('12345'),
            'is_active' => true,
            'is_admin' => true,
            'fullname' => 'Administrator',
        ]);
        User::insert([
            'username' => 'kasir',
            'password' => Hash::make('12345'),
            'is_active' => true,
            'is_admin' => false,
            'fullname' => 'Kasir',
        ]);

        // $faker = \Faker\Factory::create('id_ID');
        // DB::beginTransaction();
        // $pw = Hash::make('12345');
        // for ($i = 3; $i <= 100; $i++) {
        //     User::insert([
        //         'id' => $i,
        //         'username' => 'user' . $i,
        //         'password' => $pw,
        //         'is_active' => rand(0, 1),
        //         'is_admin' => rand(0, 1),
        //         'fullname' => $faker->name(),
        //         'group_id' => rand(1, 2),
        //     ]);
        // }
        // DB::commit();
    }
}
