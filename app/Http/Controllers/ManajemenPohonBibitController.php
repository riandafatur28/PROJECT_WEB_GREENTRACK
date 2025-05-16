<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\Log;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class ManajemenPohonBibitController extends Controller
{
    /**
     * Helper function to safely extract location values
     */
    private function extractLocationValue($data, $key)
    {
        // Try direct access
        if (isset($data[$key])) {
            if (is_string($data[$key])) {
                return $data[$key];
            } elseif (isset($data[$key]['stringValue'])) {
                return $data[$key]['stringValue'];
            } elseif (isset($data[$key]['mapValue']) && isset($data[$key]['mapValue']['fields'])) {
                // Handle nested mapValue structure
                $mapFields = [];
                foreach ($data[$key]['mapValue']['fields'] as $fieldKey => $fieldValue) {
                    if (isset($fieldValue['stringValue'])) {
                        $mapFields[] = $fieldValue['stringValue'];
                    }
                }
                return !empty($mapFields) ? implode(', ', $mapFields) : 'Tidak tersedia';
            }
        }
        return 'Tidak tersedia';
    }

    /**
     * Improved location extraction for nested maps with correct order
     */
    private function extractNestedLocation($locationData)
    {
        if (!is_array($locationData)) {
            return 'Tidak tersedia';
        }

        // Initialize location parts with correct order RPH, BKPH, KPH
        $locationParts = [
            'rkph' => null,
            'bkph' => null,
            'kph' => null,
        ];

        // Check for mapValue structure
        if (isset($locationData['mapValue']) && isset($locationData['mapValue']['fields'])) {
            $fields = $locationData['mapValue']['fields'];

            // Extract location fields in correct order
            if (isset($fields['rkph']) && isset($fields['rkph']['stringValue']) && !empty($fields['rkph']['stringValue'])) {
                $locationParts['rkph'] = $fields['rkph']['stringValue'];
            }

            if (isset($fields['bkph']) && isset($fields['bkph']['stringValue']) && !empty($fields['bkph']['stringValue'])) {
                $locationParts['bkph'] = $fields['bkph']['stringValue'];
            }

            if (isset($fields['kph']) && isset($fields['kph']['stringValue']) && !empty($fields['kph']['stringValue'])) {
                $locationParts['kph'] = $fields['kph']['stringValue'];
            }

            // Add extra location info if needed
            if (isset($fields['alamat']) && isset($fields['alamat']['stringValue']) && !empty($fields['alamat']['stringValue'])) {
                $locationParts['alamat'] = $fields['alamat']['stringValue'];
            }

            if (isset($fields['bagian_hutan']) && (isset($fields['bagian_hutan']['stringValue']) || isset($fields['bagian_hutan']['numberValue']))) {
                $value = isset($fields['bagian_hutan']['stringValue']) ?
                    $fields['bagian_hutan']['stringValue'] : $fields['bagian_hutan']['numberValue'];
                if (!empty($value)) {
                    $locationParts['bagian_hutan'] = "BH " . $value;
                }
            }
        } else {
            // Direct field access without mapValue
            if (isset($locationData['rkph']) && isset($locationData['rkph']['stringValue']) && !empty($locationData['rkph']['stringValue'])) {
                $locationParts['rkph'] = $locationData['rkph']['stringValue'];
            }

            if (isset($locationData['bkph']) && isset($locationData['bkph']['stringValue']) && !empty($locationData['bkph']['stringValue'])) {
                $locationParts['bkph'] = $locationData['bkph']['stringValue'];
            }

            if (isset($locationData['kph']) && isset($locationData['kph']['stringValue']) && !empty($locationData['kph']['stringValue'])) {
                $locationParts['kph'] = $locationData['kph']['stringValue'];
            }

            if (isset($locationData['alamat']) && isset($locationData['alamat']['stringValue']) && !empty($locationData['alamat']['stringValue'])) {
                $locationParts['alamat'] = $locationData['alamat']['stringValue'];
            }

            if (isset($locationData['bagian_hutan']) && (isset($locationData['bagian_hutan']['stringValue']) || isset($locationData['bagian_hutan']['numberValue']))) {
                $value = isset($locationData['bagian_hutan']['stringValue']) ?
                    $locationData['bagian_hutan']['stringValue'] : $locationData['bagian_hutan']['numberValue'];
                if (!empty($value)) {
                    $locationParts['bagian_hutan'] = "BH " . $value;
                }
            }
        }

        // Filter out null values and join with commas
        $validParts = array_filter($locationParts);
        return !empty($validParts) ? implode(', ', $validParts) : 'Tidak tersedia';
    }

    /**
     * Format timestamp for better display
     */
    private function formatTimestamp($timestamp)
    {
        if (empty($timestamp) || $timestamp === '-') {
            return 'Tidak tersedia';
        }

        try {
            // Check if it's a direct timestamp value
            if (is_string($timestamp) && strtotime($timestamp)) {
                return date('d M Y', strtotime($timestamp));
            }

            // For unix timestamps stored as numbers
            if (is_numeric($timestamp)) {
                // Convert milliseconds to seconds if needed
                if (strlen($timestamp) > 10) {
                    $timestamp = (int)($timestamp / 1000);
                }
                return date('d M Y', $timestamp);
            }

            return $timestamp;
        } catch (\Exception $e) {
            Log::error('Error formatting timestamp: ' . $e->getMessage());
            return 'Format tanggal tidak valid';
        }
    }

    public function index(Request $request, FirestoreService $firestore)
    {
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $perPage = 10;
        $sort = $request->input('sort', 'terbaru');
        $tab = $request->input('tab', 'bibit');

        // Initialize
        $bibit = [];
        $kayu = [];
        $errorMessage = null;

        try {
            // Ambil data bibit dan kayu dari Firestore
            $bibitResponse = $firestore->getCollection('bibit');
            $kayuResponse = $firestore->getCollection('kayu');

            // Proses data bibit
            if (isset($bibitResponse['documents'])) {
                foreach ($bibitResponse['documents'] as $document) {
                    $fields = $document['fields'] ?? [];
                    $id = basename($document['name']);

                    // Penanganan gambar yang lebih baik
                    $gambarUrl = $this->extractImageUrl($fields['gambar_image'] ?? null);

                    // Enhanced location extraction
                    $lokasi = $this->extractNestedLocation($fields['lokasi_tanam'] ?? []);

                    // Convert numeric values to string as requested
                    $tinggiValue = isset($fields['tinggi']['integerValue'])
                        ? (string)$fields['tinggi']['integerValue']
                        : (isset($fields['tinggi']['numberValue']) ? (string)$fields['tinggi']['numberValue'] : '0');

                    $usiaValue = isset($fields['usia']['integerValue'])
                        ? (string)$fields['usia']['integerValue']
                        : (isset($fields['usia']['numberValue']) ? (string)$fields['usia']['numberValue'] : '0');

                    // Format dates
                    $tanggalPembibitan = '';
                    if (isset($fields['tanggal_pembibitan']['timestampValue'])) {
                        $tanggalPembibitan = $this->formatTimestamp($fields['tanggal_pembibitan']['timestampValue']);
                    } elseif (isset($fields['tanggal_pembibitanFl']['timestampValue'])) {
                        $tanggalPembibitan = $this->formatTimestamp($fields['tanggal_pembibitanFl']['timestampValue']);
                    } elseif (isset($fields['tanggal_pembibitanSl']['stringValue'])) {
                        $tanggalPembibitan = $fields['tanggal_pembibitanSl']['stringValue'];
                    }

                    $createdAt = isset($fields['created_at']['timestampValue']) ?
                        strtotime($fields['created_at']['timestampValue']) : 0;

                    $namaBibit = $fields['nama_bibit']['stringValue'] ?? 'Tidak tersedia';
                    $jenisBibit = $fields['jenis_bibit']['stringValue'] ?? 'Tidak tersedia';
                    $idBibit = $fields['id_bibit']['stringValue'] ?? 'Tidak tersedia';

                    // Use the generated location in your array with new fields
                    $bibit[] = [
                        'id' => $id,
                        'id_bibit' => $idBibit,
                        'nama_bibit' => $namaBibit,
                        'jenis_bibit' => $jenisBibit,
                        'asal_bibit' => $fields['asal_bibit']['stringValue'] ?? 'Tidak tersedia',
                        'status' => $fields['kondisi']['stringValue'] ?? 'Penyemaian',
                        'produktivitas' => $fields['produktivitas']['stringValue'] ?? 'Tidak tersedia',
                        'lokasi' => $lokasi,
                        'media_tanam' => $fields['media_tanam']['stringValue'] ?? 'Tidak tersedia',
                        'nutrisi' => $fields['nutrisi']['stringValue'] ?? 'Tidak tersedia',
                        'status_hama' => $fields['status_hama']['stringValue'] ?? 'Tidak tersedia',
                        'catatan' => $fields['catatan']['stringValue'] ?? 'Tidak tersedia',
                        'gambar_image' => $gambarUrl,
                        'tinggi' => $tinggiValue,
                        'varietas' => $fields['varietas']['stringValue'] ?? 'Tidak tersedia',
                        'tanggal_pembibitan' => $tanggalPembibitan,
                        'tanggal_pembibitanFl' => isset($fields['tanggal_pembibitanFl']['timestampValue']) ?
                            $this->formatTimestamp($fields['tanggal_pembibitanFl']['timestampValue']) : 'Tidak tersedia',
                        'tanggal_pembibitanSl' => $fields['tanggal_pembibitanSl']['stringValue'] ?? 'Tidak tersedia',
                        'usia' => $usiaValue,
                        'deskripsi' => $fields['deskripsi']['stringValue'] ?? 'Tidak tersedia',
                        'created_at' => isset($fields['created_at']['timestampValue']) ?
                            $this->formatTimestamp($fields['created_at']['timestampValue']) : 'Tidak tersedia',
                        'updated_at' => isset($fields['updated_at']['timestampValue']) ?
                            $this->formatTimestamp($fields['updated_at']['timestampValue']) : 'Tidak tersedia',
                        'id_user' => $fields['id_user']['stringValue'] ?? 'Tidak tersedia',
                        'raw_gambar' => $fields['gambar_image'] ?? null,
                        'created_at_timestamp' => $createdAt,
                    ];
                }
            }

            // Proses data kayu
            if (isset($kayuResponse['documents'])) {
                foreach ($kayuResponse['documents'] as $document) {
                    $fields = $document['fields'] ?? [];
                    $id = basename($document['name']);

                    // Ambil gambar kayu dari Firestore
                    $gambarUrl = $this->extractImageUrl($fields['gambar_image'] ?? null);

                    // Enhanced location extraction
                    $lokasi = $this->extractNestedLocation($fields['lokasi_tanam'] ?? []);

                    // Convert numeric values to string as requested
                    $tinggiValue = isset($fields['tinggi']['integerValue'])
                        ? (string)$fields['tinggi']['integerValue']
                        : (isset($fields['tinggi']['numberValue']) ? (string)$fields['tinggi']['numberValue'] : '0');

                    // Make sure tinggi is not zero for display
                    if ($tinggiValue === '0') {
                        $tinggiValue = isset($fields['panjang']['integerValue'])
                            ? (string)$fields['panjang']['integerValue']
                            : (isset($fields['panjang']['numberValue']) ? (string)$fields['panjang']['numberValue'] : '10'); // Default to 10 if no value
                    }

                    $jumlahStokValue = isset($fields['jumlah_stok']['integerValue'])
                        ? (string)$fields['jumlah_stok']['integerValue']
                        : (isset($fields['jumlah_stok']['numberValue']) ? (string)$fields['jumlah_stok']['numberValue'] : '0');

                    $usiaValue = isset($fields['usia']['integerValue'])
                        ? (string)$fields['usia']['integerValue']
                        : (isset($fields['usia']['numberValue']) ? (string)$fields['usia']['numberValue'] : '0');

                    // Handle tanggal_lahir_pohon and tanggal_panen
                    $tanggalLahirPohon = 'Tidak tersedia';
                    if (isset($fields['tanggal_lahir_pohon']['integerValue'])) {
                        $tanggalLahirPohon = $this->formatTimestamp($fields['tanggal_lahir_pohon']['integerValue']);
                    } elseif (isset($fields['tanggal_lahir_pohon']['numberValue'])) {
                        $tanggalLahirPohon = $this->formatTimestamp($fields['tanggal_lahir_pohon']['numberValue']);
                    } elseif (isset($fields['tanggal_lahir_pohon']['timestampValue'])) {
                        $tanggalLahirPohon = $this->formatTimestamp($fields['tanggal_lahir_pohon']['timestampValue']);
                    }

                    $tanggalPanen = 'Tidak tersedia';
                    if (isset($fields['tanggal_panen']['timestampValue'])) {
                        $tanggalPanen = $this->formatTimestamp($fields['tanggal_panen']['timestampValue']);
                    }

                    $createdAt = isset($fields['created_at']['timestampValue']) ?
                        strtotime($fields['created_at']['timestampValue']) : 0;

                    $namaKayu = $fields['nama_kayu']['stringValue'] ?? 'Tidak tersedia';
                    $jenisKayu = $fields['jenis_kayu']['stringValue'] ?? 'Tidak tersedia';
                    $idKayu = $fields['id_kayu']['stringValue'] ?? 'Tidak tersedia';

                    // Ambil data kayu dari Firestore dengan fields baru
                    $kayu[] = [
                        'id' => $id,
                        'id_kayu' => $idKayu,
                        'nama_kayu' => $namaKayu,
                        'jenis_kayu' => $jenisKayu,
                        'barcode' => $fields['barcode']['stringValue'] ?? 'Tidak tersedia',
                        'status' => $jumlahStokValue > '0' ? 'Tersedia' : 'Kosong',
                        'lokasi' => $lokasi,
                        'tinggi' => $tinggiValue,
                        'gambar_image' => $gambarUrl,
                        'batch_panen' => $fields['batch_panen']['stringValue'] ?? 'Tidak tersedia',
                        'varietas' => $fields['varietas']['stringValue'] ?? 'Tidak tersedia',
                        'tanggal_panen' => $tanggalPanen,
                        'tanggal_lahir_pohon' => $tanggalLahirPohon,
                        'jumlah_stok' => $jumlahStokValue,
                        'usia' => $usiaValue,
                        'catatan' => $fields['catatan']['stringValue'] ?? 'Tidak tersedia',
                        'created_at' => isset($fields['created_at']['timestampValue']) ?
                            $this->formatTimestamp($fields['created_at']['timestampValue']) : 'Tidak tersedia',
                        'updated_at' => isset($fields['updated_at']['timestampValue']) ?
                            $this->formatTimestamp($fields['updated_at']['timestampValue']) : 'Tidak tersedia',
                        'id_user' => $fields['id_user']['stringValue'] ?? 'Tidak tersedia',
                        'raw_gambar' => $fields['gambar_image'] ?? null,
                        'created_at_timestamp' => $createdAt,
                    ];
                }
            }

        } catch (ConnectException $e) {
            Log::error('Firestore connectivity error: ' . $e->getMessage());
            $errorMessage = 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda dan coba lagi nanti.';
        } catch (RequestException $e) {
            Log::error('Firestore request error: ' . $e->getMessage());
            if ($e->getCode() == 6) { // cURL error 6: Could not resolve host
                $errorMessage = 'Koneksi tidak stabil. Silakan periksa koneksi internet Anda dan coba lagi nanti.';
            } else {
                $errorMessage = 'Terjadi kesalahan saat menghubungi server Firestore: ' . $e->getMessage();
            }
        } catch (Exception $e) {
            Log::error('General error in Firestore access: ' . $e->getMessage());
            $errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        // Apply search filter after collecting data
        if (!empty($search)) {
            $bibit = array_filter($bibit, function($item) use ($search) {
                // Search in multiple fields
                return stripos($item['nama_bibit'], $search) !== false
                    || stripos($item['id_bibit'], $search) !== false
                    || stripos($item['jenis_bibit'], $search) !== false
                    || stripos($item['tinggi'], $search) !== false
                    || stripos($item['lokasi'], $search) !== false;
            });

            $kayu = array_filter($kayu, function($item) use ($search) {
                // Search in multiple fields
                return stripos($item['nama_kayu'], $search) !== false
                    || stripos($item['id_kayu'], $search) !== false
                    || stripos($item['jenis_kayu'], $search) !== false
                    || stripos($item['tinggi'], $search) !== false
                    || stripos($item['lokasi'], $search) !== false;
            });
        }

        // Apply sorting
        if ($sort === 'terbaru') {
            // Sort by created_at_timestamp (newest first)
            usort($bibit, function($a, $b) {
                return $b['created_at_timestamp'] - $a['created_at_timestamp'];
            });

            usort($kayu, function($a, $b) {
                return $b['created_at_timestamp'] - $a['created_at_timestamp'];
            });
        } else {
            // Sort by created_at_timestamp (oldest first)
            usort($bibit, function($a, $b) {
                return $a['created_at_timestamp'] - $b['created_at_timestamp'];
            });

            usort($kayu, function($a, $b) {
                return $a['created_at_timestamp'] - $b['created_at_timestamp'];
            });
        }

        $totalBibit = count($bibit);
        $totalKayu = count($kayu);

        // Pagination
        $offsetBibit = ($page - 1) * $perPage;
        $offsetKayu = ($page - 1) * $perPage;

        $paginatedBibit = array_slice($bibit, $offsetBibit, $perPage);
        $paginatedKayu = array_slice($kayu, $offsetKayu, $perPage);

        // Calculate pagination info
        $lastPage = ceil(max($totalBibit, $totalKayu) / $perPage);

        // If lastPage is 0, set it to 1 to avoid pagination issues
        $lastPage = max(1, $lastPage);

        return view('layouts.manajemenkayubibit', [
            'bibit' => $paginatedBibit,
            'kayu' => $paginatedKayu,
            'totalBibit' => $totalBibit,
            'totalKayu' => $totalKayu,
            'currentPage' => $page,
            'perPage' => $perPage,
            'lastPage' => $lastPage,
            'search' => $search,
            'sort' => $sort,
            'tab' => $tab,
            'errorMessage' => $errorMessage,
        ]);
    }

    /**
     * Fungsi helper untuk mengekstrak URL gambar dari berbagai kemungkinan struktur data
     */
    private function extractImageUrl($imageField)
    {
        // Default image
        $defaultImage = 'https://via.placeholder.com/250';

        if (empty($imageField)) {
            return $defaultImage;
        }

        // Jika gambar_image adalah array values
        if (isset($imageField['arrayValue']) && isset($imageField['arrayValue']['values'])) {
            $values = $imageField['arrayValue']['values'];
            if (!empty($values) && isset($values[0]['stringValue'])) {
                return $values[0]['stringValue'];
            }
        }

        // Jika gambar_image langsung berisi array dengan indeks numerik
        if (is_array($imageField) && isset($imageField[0]['stringValue'])) {
            return $imageField[0]['stringValue'];
        }

        // Jika gambar_image adalah string langsung
        if (isset($imageField['stringValue'])) {
            return $imageField['stringValue'];
        }

        // Log struktur gambar yang tidak dikenali untuk debugging
        Log::warning('Struktur gambar tidak dikenali: ' . json_encode($imageField));

        return $defaultImage;
    }

    // Tambahkan method untuk mengambil detail bibit untuk modal
    public function getBibitDetail(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');

        try {
            // Ambil detail bibit dari Firestore
            $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/bibit/{$id}";
            $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->get($url);

            if ($response->successful()) {
                $document = $response->json();
                $fields = $document['fields'] ?? [];

                // Debug untuk melihat struktur data gambar
                if (isset($fields['gambar_image'])) {
                    Log::info('Detail Bibit - Struktur gambar: ' . json_encode($fields['gambar_image']));
                }

                // Ekstrak URL gambar
                $gambarUrl = $this->extractImageUrl($fields['gambar_image'] ?? null);

                // Enhanced location extraction with correct order
                $lokasi = $this->extractNestedLocation($fields['lokasi_tanam'] ?? []);

                // Convert numeric values to string
                $tinggiValue = isset($fields['tinggi']['integerValue'])
                    ? (string)$fields['tinggi']['integerValue']
                    : (isset($fields['tinggi']['numberValue']) ? (string)$fields['tinggi']['numberValue'] : '0');

                $usiaValue = isset($fields['usia']['integerValue'])
                    ? (string)$fields['usia']['integerValue']
                    : (isset($fields['usia']['numberValue']) ? (string)$fields['usia']['numberValue'] : '0');

                // Format dates
                $tanggalPembibitan = 'Tidak tersedia';
                if (isset($fields['tanggal_pembibitan']['timestampValue'])) {
                    $tanggalPembibitan = $this->formatTimestamp($fields['tanggal_pembibitan']['timestampValue']);
                } elseif (isset($fields['tanggal_pembibitanFl']['timestampValue'])) {
                    $tanggalPembibitan = $this->formatTimestamp($fields['tanggal_pembibitanFl']['timestampValue']);
                } elseif (isset($fields['tanggal_pembibitanSl']['stringValue'])) {
                    $tanggalPembibitan = $fields['tanggal_pembibitanSl']['stringValue'];
                }

                $detail = [
                    'id' => $id,
                    'id_bibit' => $fields['id_bibit']['stringValue'] ?? 'Tidak tersedia',
                    'nama_bibit' => $fields['nama_bibit']['stringValue'] ?? 'Tidak tersedia',
                    'jenis_bibit' => $fields['jenis_bibit']['stringValue'] ?? 'Tidak tersedia',
                    'asal_bibit' => $fields['asal_bibit']['stringValue'] ?? 'Tidak tersedia',
                    'status' => $fields['kondisi']['stringValue'] ?? 'Penyemaian',
                    'produktivitas' => $fields['produktivitas']['stringValue'] ?? 'Tidak tersedia',
                    'lokasi' => $lokasi,
                    'media_tanam' => $fields['media_tanam']['stringValue'] ?? 'Tidak tersedia',
                    'nutrisi' => $fields['nutrisi']['stringValue'] ?? 'Tidak tersedia',
                    'status_hama' => $fields['status_hama']['stringValue'] ?? 'Tidak tersedia',
                    'catatan' => $fields['catatan']['stringValue'] ?? 'Tidak tersedia',
                    'gambar_image' => $gambarUrl,
                    'tinggi' => $tinggiValue,
                    'varietas' => $fields['varietas']['stringValue'] ?? 'Tidak tersedia',
                    'tanggal_pembibitan' => $tanggalPembibitan,
                    'tanggal_pembibitanFl' => isset($fields['tanggal_pembibitanFl']['timestampValue']) ?
                        $this->formatTimestamp($fields['tanggal_pembibitanFl']['timestampValue']) : 'Tidak tersedia',
                    'tanggal_pembibitanSl' => $fields['tanggal_pembibitanSl']['stringValue'] ?? 'Tidak tersedia',
                    'usia' => $usiaValue,
                    'deskripsi' => $fields['deskripsi']['stringValue'] ?? 'Tidak tersedia',
                    'created_at' => isset($fields['created_at']['timestampValue']) ?
                        $this->formatTimestamp($fields['created_at']['timestampValue']) : 'Tidak tersedia',
                    'updated_at' => isset($fields['updated_at']['timestampValue']) ?
                        $this->formatTimestamp($fields['updated_at']['timestampValue']) : 'Tidak tersedia',
                    'id_user' => $fields['id_user']['stringValue'] ?? 'Tidak tersedia',
                ];

                return response()->json(['success' => true, 'data' => $detail]);
            }

            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);

        } catch (ConnectException $e) {
            Log::error('Firestore connectivity error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            Log::error('Firestore request error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // Tambahkan method untuk mengambil detail kayu untuk modal
    public function getKayuDetail(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');

        try {
            // Ambil detail kayu dari Firestore
            $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/kayu/{$id}";
            $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->get($url);

            if ($response->successful()) {
                $document = $response->json();
                $fields = $document['fields'] ?? [];

                // Ekstrak URL gambar
                $gambarUrl = $this->extractImageUrl($fields['gambar_image'] ?? null);

                // Enhanced location extraction with correct order
                $lokasi = $this->extractNestedLocation($fields['lokasi_tanam'] ?? []);

                // Convert numeric values to string
                $tinggiValue = isset($fields['tinggi']['integerValue'])
                    ? (string)$fields['tinggi']['integerValue']
                    : (isset($fields['tinggi']['numberValue']) ? (string)$fields['tinggi']['numberValue'] : '0');

                // Make sure tinggi is not zero for display
                if ($tinggiValue === '0') {
                    $tinggiValue = isset($fields['panjang']['integerValue'])
                        ? (string)$fields['panjang']['integerValue']
                        : (isset($fields['panjang']['numberValue']) ? (string)$fields['panjang']['numberValue'] : '10'); // Default to 10 if no value
                }

                $jumlahStokValue = isset($fields['jumlah_stok']['integerValue'])
                    ? (string)$fields['jumlah_stok']['integerValue']
                    : (isset($fields['jumlah_stok']['numberValue']) ? (string)$fields['jumlah_stok']['numberValue'] : '0');

                $usiaValue = isset($fields['usia']['integerValue'])
                    ? (string)$fields['usia']['integerValue']
                    : (isset($fields['usia']['numberValue']) ? (string)$fields['usia']['numberValue'] : '0');

                // Handle tanggal_lahir_pohon and tanggal_panen
                $tanggalLahirPohon = 'Tidak tersedia';
                if (isset($fields['tanggal_lahir_pohon']['integerValue'])) {
                    $tanggalLahirPohon = $this->formatTimestamp($fields['tanggal_lahir_pohon']['integerValue']);
                } elseif (isset($fields['tanggal_lahir_pohon']['numberValue'])) {
                    $tanggalLahirPohon = $this->formatTimestamp($fields['tanggal_lahir_pohon']['numberValue']);
                } elseif (isset($fields['tanggal_lahir_pohon']['timestampValue'])) {
                    $tanggalLahirPohon = $this->formatTimestamp($fields['tanggal_lahir_pohon']['timestampValue']);
                }

                $tanggalPanen = 'Tidak tersedia';
                if (isset($fields['tanggal_panen']['timestampValue'])) {
                    $tanggalPanen = $this->formatTimestamp($fields['tanggal_panen']['timestampValue']);
                }

                $detail = [
                    'id' => $id,
                    'id_kayu' => $fields['id_kayu']['stringValue'] ?? 'Tidak tersedia',
                    'nama_kayu' => $fields['nama_kayu']['stringValue'] ?? 'Tidak tersedia',
                    'jenis_kayu' => $fields['jenis_kayu']['stringValue'] ?? 'Tidak tersedia',
                    'barcode' => $fields['barcode']['stringValue'] ?? 'Tidak tersedia',
                    'status' => $jumlahStokValue > '0' ? 'Tersedia' : 'Kosong',
                    'lokasi' => $lokasi,
                    'tinggi' => $tinggiValue,
                    'gambar_image' => $gambarUrl,
                    'batch_panen' => $fields['batch_panen']['stringValue'] ?? 'Tidak tersedia',
                    'varietas' => $fields['varietas']['stringValue'] ?? 'Tidak tersedia',
                    'tanggal_panen' => $tanggalPanen,
                    'tanggal_lahir_pohon' => $tanggalLahirPohon,
                    'jumlah_stok' => $jumlahStokValue,
                    'usia' => $usiaValue,
                    'catatan' => $fields['catatan']['stringValue'] ?? 'Tidak tersedia',
                    'created_at' => isset($fields['created_at']['timestampValue']) ?
                        $this->formatTimestamp($fields['created_at']['timestampValue']) : 'Tidak tersedia',
                    'updated_at' => isset($fields['updated_at']['timestampValue']) ?
                        $this->formatTimestamp($fields['updated_at']['timestampValue']) : 'Tidak tersedia',
                    'id_user' => $fields['id_user']['stringValue'] ?? 'Tidak tersedia',
                ];

                return response()->json(['success' => true, 'data' => $detail]);
            }

            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);

        } catch (ConnectException $e) {
            Log::error('Firestore connectivity error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            Log::error('Firestore request error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi untuk memperbarui status bibit
    public function updateBibitStatus(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');
        $status = $request->input('status');

        try {
            // URL API Firestore untuk update status bibit
            $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/bibit/{$id}?updateMask.fieldPaths=kondisi";
            $payload = [
                'fields' => [
                    'kondisi' => ['stringValue' => $status]
                ]
            ];

            // Melakukan permintaan PATCH ke Firestore
            $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->patch($url, $payload);

            return response()->json(['success' => $response->successful()]);

        } catch (ConnectException $e) {
            Log::error('Firestore connectivity error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            Log::error('Firestore request error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi untuk memperbarui status kayu
    public function updateKayuStatus(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');
        $status = $request->input('status');

        try {
            // URL API Firestore untuk update status kayu
            $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/kayu/{$id}?updateMask.fieldPaths=status";
            $payload = [
                'fields' => [
                    'status' => ['stringValue' => $status]
                ]
            ];

            // Melakukan permintaan PATCH ke Firestore
            $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->patch($url, $payload);

            return response()->json(['success' => $response->successful()]);

        } catch (ConnectException $e) {
            Log::error('Firestore connectivity error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            Log::error('Firestore request error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi untuk mengedit bibit
    public function editBibit(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');
        $data = $request->except(['id', '_token']);

        try {
            // Konversi data ke format Firestore
            $fields = [];
            foreach ($data as $key => $value) {
                if ($key !== 'gambar_image') { // Gambar perlu penanganan khusus
                    $fields[$key] = ['stringValue' => $value];
                }
            }

            // URL API Firestore untuk update bibit
            $fieldsString = implode(',', array_keys($fields));
            $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/bibit/{$id}?updateMask.fieldPaths={$fieldsString}";
            $payload = ['fields' => $fields];

            // Melakukan permintaan PATCH ke Firestore
            $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->patch($url, $payload);

            return response()->json(['success' => $response->successful()]);

        } catch (ConnectException $e) {
            Log::error('Firestore connectivity error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            Log::error('Firestore request error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi untuk mengedit kayu
    public function editKayu(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');
        $data = $request->except(['id', '_token']);

        try {
            // Konversi data ke format Firestore
            $fields = [];
            foreach ($data as $key => $value) {
                if ($key !== 'gambar_image') { // Gambar perlu penanganan khusus
                    $fields[$key] = ['stringValue' => $value];
                }
            }

            // URL API Firestore untuk update kayu
            $fieldsString = implode(',', array_keys($fields));
            $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/kayu/{$id}?updateMask.fieldPaths={$fieldsString}";
            $payload = ['fields' => $fields];

            // Melakukan permintaan PATCH ke Firestore
            $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->patch($url, $payload);

            return response()->json(['success' => $response->successful()]);

        } catch (ConnectException $e) {
            Log::error('Firestore connectivity error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            Log::error('Firestore request error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi untuk menghapus bibit
    public function deleteBibit(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');

        try {
            // URL API Firestore untuk delete bibit
            $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/bibit/{$id}";

            // Melakukan permintaan DELETE ke Firestore
            $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->delete($url);

            return response()->json(['success' => $response->successful()]);

        } catch (ConnectException $e) {
            Log::error('Firestore connectivity error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            Log::error('Firestore request error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi untuk menghapus kayu
    public function deleteKayu(Request $request, FirestoreService $firestore)
    {
        $id = $request->input('id');

        try {
            // URL API Firestore untuk delete kayu
            $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/kayu/{$id}";

            // Melakukan permintaan DELETE ke Firestore
            $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->delete($url);

            return response()->json(['success' => $response->successful()]);

        } catch (ConnectException $e) {
            Log::error('Firestore connectivity error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            Log::error('Firestore request error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
