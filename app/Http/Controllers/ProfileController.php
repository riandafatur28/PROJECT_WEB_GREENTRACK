<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    protected $firestore;
    protected $storage;
    protected $documentId = '0PpJI9n682OAFWv4tabd'; // Replace with your document ID
    protected $collection = 'akun_superadmin'; // Collection name in Firestore

    public function __construct()
    {
        try {
            $this->firestore = Firebase::firestore();
            $this->storage = Firebase::storage();
        } catch (\Exception $e) {
            Log::error('Error inisialisasi Firebase: ' . $e->getMessage());
            // Continue execution, we'll handle error in method
        }
    }

    /**
     * Menampilkan halaman profil pengguna
     */
    public function show()
    {
        try {
            // Log untuk debugging
            Log::info('Mencoba memuat profil');

            // Periksa apakah Firebase diinisialisasi dengan benar
            if (!$this->firestore) {
                Log::error('Firestore tidak diinisialisasi');
                return view('profile')->with('error', 'Error konfigurasi Firebase. Periksa log untuk detail.');
            }

            // Dapatkan dokumen dari Firestore
            Log::info('Mengambil dokumen: ' . $this->collection . '/' . $this->documentId);
            $document = $this->firestore->database()->collection($this->collection)
                ->document($this->documentId)
                ->snapshot();

            if (!$document->exists()) {
                Log::warning('Dokumen tidak ditemukan: ' . $this->collection . '/' . $this->documentId);
                return view('profile')->with('error', 'Profil tidak ditemukan');
            }

            // Dapatkan data pengguna
            $userData = $document->data();
            Log::info('Data pengguna berhasil diambil', ['userData' => $userData]);

            // Simpan data pengguna dalam session untuk akses mudah di view
            Session::put('user_nama', $userData['nama_lengkap'] ?? '');  // Fixed 'nama' to 'nama_lengkap'
            Session::put('email', $userData['email'] ?? '');  // Fixed 'email'
            Session::put('role', $userData['role'] ?? '');  // Fixed 'role'
            Session::put('profile_image', $userData['profile_image'] ?? '');  // Handling profile image

            // Anda juga dapat menyimpan field lain sesuai kebutuhan
            $positions = $userData['positions'] ?? ['', '', '', ''];
            for ($i = 1; $i <= 4; $i++) {
                Session::put("position_$i", $positions[$i-1] ?? '');
            }

            return view('profile');
        } catch (\Exception $e) {
            Log::error('Error memuat profil: ' . $e->getMessage());
            return view('profile')->with('error', 'Gagal memuat profil: ' . $e->getMessage());
        }
    }

    /**
     * Update profil pengguna
     */
    public function update(Request $request)
    {
        try {
            // Periksa apakah Firebase diinisialisasi dengan benar
            if (!$this->firestore) {
                Log::error('Firestore tidak diinisialisasi dalam method update');
                return back()->with('error', 'Error konfigurasi Firebase. Periksa log untuk detail.');
            }

            $data = [
                'nama_lengkap' => $request->input('nama'),
                'email' => $request->input('email'),
                'role' => $request->input('role'),
                'positions' => [
                    $request->input('position_1', ''),
                    $request->input('position_2', ''),
                    $request->input('position_3', ''),
                    $request->input('position_4', '')
                ],
                'updated_at' => now()->toDateTimeString()
            ];

            // Tangani upload gambar profil jika disediakan
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $fileName = 'profile_' . time() . '.' . $file->getClientOriginalExtension();

                // Upload ke Firebase Storage
                $uploadedFile = $this->storage->getBucket()->upload(
                    file_get_contents($file->getRealPath()),
                    ['name' => 'profile_images/' . $fileName]
                );

                // Dapatkan URL
                $expiry = new \DateTime('tomorrow');
                $imageUrl = $uploadedFile->signedUrl($expiry);

                // Tambahkan ke array data
                $data['profile_image'] = $imageUrl;
            }

            Log::info('Mengupdate dokumen: ' . $this->collection . '/' . $this->documentId);
            // Update dokumen di Firestore
            $this->firestore->database()->collection($this->collection)
                ->document($this->documentId)
                ->set($data, ['merge' => true]);

            return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error update profil: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }

    /**
     * Logout pengguna
     */
    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }
}
