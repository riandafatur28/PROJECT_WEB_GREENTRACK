<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManajemenKayuBibitController extends Controller
{
    public function index()
    {
        $kayu = [
            ['id' => '001', 'nama' => 'Jati', 'jenis' => 'kayu', 'usia' => '14 Hari', 'jumlah' => '100', 'lokasi' => 'Bagor', 'status' => 'Penyemaian'],
            ['id' => '002', 'nama' => 'Mahoni', 'jenis' => 'kayu', 'usia' => '30 Hari', 'jumlah' => '100', 'lokasi' => 'Rejoso', 'status' => 'Penyemaian'],
            ['id' => '003', 'nama' => 'Jati', 'jenis' => 'kayu', 'usia' => '3 Hari', 'jumlah' => '100', 'lokasi' => 'Bagor', 'status' => 'Penyemaian'],
            ['id' => '004', 'nama' => 'Mahoni', 'jenis' => 'kayu', 'usia' => '5 Hari', 'jumlah' => '100', 'lokasi' => 'Bagor', 'status' => 'Penyemaian'],
            ['id' => '005', 'nama' => 'Jati', 'jenis' => 'kayu', 'usia' => '6 Hari', 'jumlah' => '100', 'lokasi' => 'Rejoso', 'status' => 'Penyemaian'],
            ['id' => '001', 'nama' => 'Jati', 'jenis' => 'kayu', 'usia' => '14 Hari', 'jumlah' => '100', 'lokasi' => 'Bagor', 'status' => 'Penyemaian'],
            ['id' => '002', 'nama' => 'Mahoni', 'jenis' => 'kayu', 'usia' => '30 Hari', 'jumlah' => '100', 'lokasi' => 'Rejoso', 'status' => 'Penyemaian'],
            ['id' => '003', 'nama' => 'Jati', 'jenis' => 'kayu', 'usia' => '3 Hari', 'jumlah' => '100', 'lokasi' => 'Bagor', 'status' => 'Penyemaian'],
            ['id' => '004', 'nama' => 'Mahoni', 'jenis' => 'kayu', 'usia' => '5 Hari', 'jumlah' => '100', 'lokasi' => 'Bagor', 'status' => 'Penyemaian'],
            ['id' => '005', 'nama' => 'Jati', 'jenis' => 'kayu', 'usia' => '6 Hari', 'jumlah' => '100', 'lokasi' => 'Rejoso', 'status' => 'Penyemaian'],
            ['id' => '001', 'nama' => 'Jati', 'jenis' => 'kayu', 'usia' => '14 Hari', 'jumlah' => '100', 'lokasi' => 'Bagor', 'status' => 'Penyemaian'],
            ['id' => '002', 'nama' => 'Mahoni', 'jenis' => 'kayu', 'usia' => '30 Hari', 'jumlah' => '100', 'lokasi' => 'Rejoso', 'status' => 'Penyemaian'],
            ['id' => '003', 'nama' => 'Jati', 'jenis' => 'kayu', 'usia' => '3 Hari', 'jumlah' => '100', 'lokasi' => 'Bagor', 'status' => 'Penyemaian'],
            ['id' => '004', 'nama' => 'Mahoni', 'jenis' => 'kayu', 'usia' => '5 Hari', 'jumlah' => '100', 'lokasi' => 'Bagor', 'status' => 'Penyemaian'],
            ['id' => '005', 'nama' => 'Jati', 'jenis' => 'kayu', 'usia' => '6 Hari', 'jumlah' => '100', 'lokasi' => 'Rejoso', 'status' => 'Penyemaian'],
        ];

        $bibit = [
            ['id' => '001', 'nama' => 'Jati', 'jenis' => 'bibit', 'usia' => '14 Hari', 'jumlah' => '100', 'lokasi' => 'Bagor', 'status' => 'Penyemaian'],
            ['id' => '002', 'nama' => 'Mahoni', 'jenis' => 'bibit', 'usia' => '30 Hari', 'jumlah' => '100', 'lokasi' => 'Rejoso', 'status' => 'Penyemaian'],
            ['id' => '003', 'nama' => 'Jati', 'jenis' => 'bibit', 'usia' => '3 Hari', 'jumlah' => '100', 'lokasi' => 'Bagor', 'status' => 'Penyemaian'],
            ['id' => '004', 'nama' => 'Mahoni', 'jenis' => 'bibit', 'usia' => '5 Hari', 'jumlah' => '100', 'lokasi' => 'Bagor', 'status' => 'Penyemaian'],
            ['id' => '005', 'nama' => 'Jati', 'jenis' => 'bibit', 'usia' => '6 Hari', 'jumlah' => '100', 'lokasi' => 'Rejoso', 'status' => 'Penyemaian'],
        ];

        return view('layouts.manajemenkayubibit', compact('kayu', 'bibit'));
    }
}
