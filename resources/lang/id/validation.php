<?php

return [
    'required' => ':attribute harus diisi.',
    'email' => 'Format :attribute tidak valid.',
    'alpha_num' => 'Format :attribute tidak valid, gunakan hanya huruf dan angka.',
    'regex' => 'Format :attribute tidak valid.',
    'unique' => ':attribute sudah digunakan.',
    'different' => ':attribute tidak boleh sama.',
    'numeric' => ':attribute sudah digunakan.',
    'max' => [
        'string' => ':attribute terlalu panjang, maksimal :max karakter.',
    ],
    'min' => [
        'string' => ':attribute terlalu pendek, minimal :min karakter.',
    ],
    'gt' => [
        'numeric' => ':attribute harus lebih dari :value'
    ],

    // 'custom' => [
    //     'email' => [
    //         'required' => 'Alamat email harus diisi.',
    //     ],
    // ],

    'attributes' => [
        'username' => 'Username',
        'fullname' => 'Nama Lengkap',
        'name' => 'Nama',
        'email' => 'Email',
        'phone' => 'No Telepon',
        'password' => 'Kata Sandi',
        'password_confirmation' => 'Konfirmasi Kata Sandi',
        'current_password' => 'Kata Sandi Saat Ini',
        'date' => 'Tanggal',
        'description' => 'Deskripsi',
        'category_id' => 'Kategori',
        'account_id' => 'Akun / Rek',
        'from_account_id' => 'Akun / Rek',
        'to_account_id' => 'Akun / Rek',
        'notes' => 'Catatan',
        'amount' => 'Jumlah',
    ],
];
