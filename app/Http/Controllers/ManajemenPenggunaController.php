<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManajemenPenggunaController extends Controller
{
    public function index()
    {
        $admin = [
            ['id' => '001', 'nama' => 'Gita', 'email' => 'gita@gmail.com', 'peran_admin' => 'Admin Persemaian', 'status' => 'Aktif'],
            ['id' => '002', 'nama' => 'Fitri', 'email' => 'fitri@gmail.com', 'peran_admin' => 'Admin TPK', 'status' => 'Aktif'],
            ['id' => '003', 'nama' => 'Natasya', 'email' => 'natasya@gmail.com', 'peran_admin' => 'Admin Persemaian', 'status' => 'Aktif'],
            ['id' => '004', 'nama' => 'Rianda', 'email' => 'rianda@gmail.com', 'peran_admin' => 'Admin TPK', 'status' => 'Nonaktif'],
            ['id' => '005', 'nama' => 'Huda', 'email' => 'huda@gmail.com', 'peran_admin' => 'Admin Persemaian', 'status' => 'Nonaktif'],
        ];

        return view('layouts.manajemenpengguna', compact('admin'));
    }
}
