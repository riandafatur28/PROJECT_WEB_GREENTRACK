<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;

class FirestoreController extends Controller
{
    public function index(FirestoreService $firestore)
    {
        $documents = $firestore->getCollection('users');
        return response()->json($documents);
    }

    public function store(Request $request, FirestoreService $firestore)
    {
        $data = $request->only(['name', 'email']);
        $result = $firestore->createDocument('users', $data);
        return response()->json($result);
    }

    public function showForm()
    {
        return view('register');
    }

    public function handleForm(Request $request, FirestoreService $firestore)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email'
        ]);

        $firestore->createDocument('users', $request->only('name', 'email'));

        return 'Registrasi berhasil!';
    }
    public function showSuperAdminForm()
{
    return view('superadmin_register');
}

public function storeSuperAdmin(Request $request, FirestoreService $firestore)
{
    $request->validate([
        'nama_lengkap' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    $data = [
        'email' => $request->email,
        'nama_lengkap' => $request->nama_lengkap,
        'password' => $request->password, // atau gunakan hash: bcrypt($request->password)
        'kode_otp' => str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
        'role' => 'super admin',
        'created_at' => now()->toISOString(),
        'updated_at' => now()->toISOString(),
        'last_login' => null,
        'last_login_ip' => $request->ip(),
    ];

    $firestore->createDocument('akun_superadmin', $data);

    return 'Super admin berhasil terdaftar!';
}
}

/*
untuk list bibit
sub colection bibit/id_bibit/document 
untuk list kayu
sub collection kayu/id kayu/document_kayu  

untuk pengguna:
sub collection akun/ id account/ document

untuk activities
sub colelction activites/id_activities/document

*/