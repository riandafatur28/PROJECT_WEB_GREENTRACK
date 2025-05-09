<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class BibitController extends Controller
{
    public function index()
    {
        $factory = (new Factory)->withServiceAccount(base_path('storage/firebase/firebase_credentials.json'));
        $firestore = $factory->createFirestore();
        $database = $firestore->database();

        $bibitCollection = $database->collection('bibit');
        $documents = $bibitCollection->documents();

        $jumlahBibit = $documents->size();  // Dapat jumlah dokumen

        return view('layouts.dashboard', compact('jumlahBibit'));
    }
}
