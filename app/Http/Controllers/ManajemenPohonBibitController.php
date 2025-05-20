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
            $errorMessage = 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda dan coba lagi nanti.';
        } catch (RequestException $e) {
            if ($e->getCode() == 6) { // cURL error 6: Could not resolve host
                $errorMessage = 'Koneksi tidak stabil. Silakan periksa koneksi internet Anda dan coba lagi nanti.';
            } else {
                $errorMessage = 'Terjadi kesalahan saat menghubungi server Firestore: ' . $e->getMessage();
            }
        } catch (Exception $e) {
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

        try {
            // Jika field kosong atau null
        if (empty($imageField)) {
            return $defaultImage;
        }

            // Log struktur data untuk debugging
            Log::info('Struktur data gambar:', ['imageField' => $imageField]);

            // Jika gambar_image adalah arrayValue Firestore
            if (isset($imageField['arrayValue'])) {
                // Jika values ada dan tidak kosong
                if (isset($imageField['arrayValue']['values']) && !empty($imageField['arrayValue']['values'])) {
            $values = $imageField['arrayValue']['values'];
                    // Ambil URL dari stringValue atau langsung dari string
                    foreach ($values as $value) {
                        if (isset($value['stringValue'])) {
                            return $value['stringValue'];
                        } elseif (is_string($value)) {
                            return $value;
                        }
                    }
                }
                // Jika array kosong atau tidak ada values, return default
                return $defaultImage;
            }

            // Jika gambar_image adalah array langsung
            if (is_array($imageField) && isset($imageField[0])) {
                if (is_string($imageField[0])) {
                    return $imageField[0];
                } elseif (isset($imageField[0]['stringValue'])) {
            return $imageField[0]['stringValue'];
                }
        }

        // Jika gambar_image adalah string langsung
            if (is_string($imageField)) {
                return $imageField;
            }

            // Jika gambar_image adalah objek dengan stringValue
        if (isset($imageField['stringValue'])) {
            return $imageField['stringValue'];
        }

            // Log jika struktur tidak dikenali
            Log::warning('Struktur gambar tidak dikenali:', ['data' => $imageField]);

        return $defaultImage;
        } catch (\Exception $e) {
            Log::error('Error dalam extractImageUrl:', [
                'message' => $e->getMessage(),
                'data' => $imageField
            ]);
        return $defaultImage;
        }
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
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
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
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
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
            $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/bibit/{$id}?updateMask.fieldPaths=status";
            $payload = [
                'fields' => [
                    'status' => ['stringValue' => $status]
                ]
            ];

            // Melakukan permintaan PATCH ke Firestore
            $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->patch($url, $payload);

            if (!$response->successful()) {
                throw new Exception('Gagal memperbarui status bibit: ' . $response->body());
            }

            return response()->json(['success' => true]);

        } catch (ConnectException $e) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
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

            if (!$response->successful()) {
                throw new Exception('Gagal memperbarui status kayu: ' . $response->body());
            }

            return response()->json(['success' => true]);

        } catch (ConnectException $e) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
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
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
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
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke server Firestore. Silakan periksa koneksi internet Anda.'], 503);
        } catch (RequestException $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi untuk menghapus bibit
    public function deleteBibit($id, FirestoreService $firestore)
    {
        try {
            // URL API Firestore untuk delete bibit
            $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/bibit/{$id}";

            // Melakukan permintaan DELETE ke Firestore
            $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->delete($url);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bibit berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus bibit: ' . $response->body()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Fungsi untuk menghapus kayu
    public function deleteKayu($id, FirestoreService $firestore)
    {
        try {
            // URL API Firestore untuk delete kayu
            $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/kayu/{$id}";

            // Melakukan permintaan DELETE ke Firestore
            $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->delete($url);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kayu berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus kayu: ' . $response->body()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Fungsi untuk mengupdate bibit
    public function updateBibit(Request $request, FirestoreService $firestore, $id)
    {
        try {
            Log::info('Starting bibit update', [
                'id' => $id,
                'request_data' => $request->all()
            ]);

            // First, verify the document exists
            $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/bibit/{$id}";
            $existingDoc = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->get($url);

            if (!$existingDoc->successful()) {
                Log::error('Document not found', [
                    'id' => $id,
                    'response' => $existingDoc->body()
                ]);
                throw new \Exception('Document not found');
            }

            $existingFields = $existingDoc->json()['fields'] ?? [];
            Log::info('Existing document fields:', ['fields' => $existingFields]);

            // Prepare fields to update
            $fields = [];
            $fieldPaths = [];

            // Handle numeric fields with proper type conversion
            if ($request->has('tinggi')) {
                $tinggi = intval($request->input('tinggi'));
                $fields['tinggi'] = ['integerValue' => $tinggi];
                $fieldPaths[] = 'tinggi';
            }

            if ($request->has('usia')) {
                $usia = intval($request->input('usia'));
                $fields['usia'] = ['integerValue' => $usia];
                $fieldPaths[] = 'usia';
            }

            // Handle string fields
            $stringFields = [
                'nama_bibit', 'jenis_bibit', 'varietas', 'asal_bibit', 
                'produktivitas', 'kondisi', 'media_tanam', 'nutrisi', 
                'status_hama', 'catatan'
            ];

            foreach ($stringFields as $field) {
                if ($request->has($field)) {
                    $value = $request->input($field);
                    if (!is_null($value) && $value !== '') {
                        $fields[$field] = ['stringValue' => (string)$value];
                        $fieldPaths[] = $field;
                    }
                }
            }

            // Handle image upload if present
            if ($request->hasFile('gambar_image')) {
                $image = $request->file('gambar_image');
                
                // Validate image
                $request->validate([
                    'gambar_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);

                try {
                    $client = new \GuzzleHttp\Client();
                    
                    $formData = [
                        [
                            'name' => 'image',
                            'contents' => fopen($image->getRealPath(), 'r'),
                            'filename' => $image->getClientOriginalName()
                        ]
                    ];

                    $imgbbResponse = $client->request('POST', 'https://api.imgbb.com/1/upload', [
                        'multipart' => $formData,
                        'query' => [
                            'key' => '3c2af4d518d6ccc3c2d7d6f86bd7a1dc'
                        ]
                    ]);

                    $imgbbData = json_decode($imgbbResponse->getBody(), true);
                    
                    if (isset($imgbbData['data']['url'])) {
                        $imageUrl = $imgbbData['data']['url'];
                        // Store image URL as array in Firestore format
                        $fields['gambar_image'] = [
                            'arrayValue' => [
                                'values' => [
                                    ['stringValue' => $imageUrl]
                                ]
                            ]
                        ];
                        $fieldPaths[] = 'gambar_image';
                        Log::info('Image uploaded successfully', ['url' => $imageUrl]);
                    }
                } catch (\Exception $e) {
                    Log::error('Image upload failed', [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Update timestamp
            $fields['updated_at'] = [
                'timestampValue' => date('c')
            ];
            $fieldPaths[] = 'updated_at';

            if (empty($fieldPaths)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No fields to update'
                ], 400);
            }

            // Create update mask query string
            $updateMaskPaths = implode('&', array_map(function($path) {
                return "updateMask.fieldPaths=" . urlencode($path);
            }, array_unique($fieldPaths)));

            // Prepare update URL
            $updateUrl = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/bibit/{$id}?{$updateMaskPaths}";
            
            Log::info('Sending update to Firestore', [
                'url' => $updateUrl,
                'fields' => $fields
            ]);

            // Make the update request
            $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->patch($updateUrl, [
                    'fields' => $fields
                ]);

            if (!$response->successful()) {
                Log::error('Firestore update failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Firestore update failed: ' . $response->body());
            }

            Log::info('Update successful', [
                'response' => $response->json()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data updated successfully',
                'updated_fields' => $fieldPaths
            ]);

        } catch (\Exception $e) {
            Log::error('Update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Fungsi untuk mengupdate kayu
    public function updateKayu(Request $request, FirestoreService $firestore, $id)
    {
        try {
            Log::info('Starting kayu update', [
                'id' => $id,
                'request_data' => $request->all()
            ]);

            // First, verify the document exists and get current data
            $url = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/kayu/{$id}";
            $existingDoc = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->get($url);

            if (!$existingDoc->successful()) {
                Log::error('Document not found', [
                    'id' => $id,
                    'response' => $existingDoc->body()
                ]);
                throw new \Exception('Document not found');
            }

            $existingFields = $existingDoc->json()['fields'] ?? [];
            Log::info('Existing document fields:', ['fields' => $existingFields]);

            // Start with existing fields to preserve all data
            $fields = $existingFields;

            // Handle numeric fields with proper type conversion
            if ($request->has('tinggi')) {
                $tinggi = floatval($request->input('tinggi'));
                $fields['tinggi'] = ['doubleValue' => $tinggi];
            }

            if ($request->has('usia')) {
                $usia = intval($request->input('usia'));
                $fields['usia'] = ['integerValue' => $usia];
            }

            if ($request->has('jumlah_stok')) {
                $jumlahStok = intval($request->input('jumlah_stok'));
                $fields['jumlah_stok'] = ['integerValue' => $jumlahStok];
            }

            // Handle string fields
            $stringFields = [
                'nama_kayu', 'jenis_kayu', 'varietas', 'barcode',
                'batch_panen', 'catatan', 'status', 'id_user'
            ];

            foreach ($stringFields as $field) {
                if ($request->has($field) && $request->input($field) !== null) {
                    $fields[$field] = ['stringValue' => (string)$request->input($field)];
                }
            }

            // Handle lokasi_tanam as a map
            $lokasiFields = ['kph', 'luas_petak', 'rkph', 'bkph', 'alamat'];
            $lokasiUpdated = false;
            $lokasiMap = isset($fields['lokasi_tanam']) ? $fields['lokasi_tanam'] : [
                'mapValue' => [
                    'fields' => [
                        'kph' => ['stringValue' => ''],
                        'luas_petak' => ['stringValue' => ''],
                        'rkph' => ['stringValue' => ''],
                        'lng' => ['integerValue' => 0],
                        'lat' => ['integerValue' => 0],
                        'bkph' => ['stringValue' => ''],
                        'alamat' => ['stringValue' => '']
                    ]
                ]
            ];

            foreach ($lokasiFields as $field) {
                if ($request->has("lokasi_$field")) {
                    $lokasiMap['mapValue']['fields'][$field]['stringValue'] = $request->input("lokasi_$field");
                    $lokasiUpdated = true;
                }
            }

            if ($lokasiUpdated) {
                $fields['lokasi_tanam'] = $lokasiMap;
            }

            // Handle image upload if present
            if ($request->hasFile('gambar_image')) {
                $image = $request->file('gambar_image');
                
                // Validate image
                $request->validate([
                    'gambar_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);

                try {
                    $client = new \GuzzleHttp\Client();
                    
                    $formData = [
                        [
                            'name' => 'image',
                            'contents' => fopen($image->getRealPath(), 'r'),
                            'filename' => $image->getClientOriginalName()
                        ]
                    ];

                    $imgbbResponse = $client->request('POST', 'https://api.imgbb.com/1/upload', [
                        'multipart' => $formData,
                        'query' => [
                            'key' => '3c2af4d518d6ccc3c2d7d6f86bd7a1dc'
                        ]
                    ]);

                    $imgbbData = json_decode($imgbbResponse->getBody(), true);
                    
                    if (isset($imgbbData['data']['url'])) {
                        $imageUrl = $imgbbData['data']['url'];
                        // Store image URL as array in Firestore format
                        $fields['gambar_image'] = [
                            'arrayValue' => [
                                'values' => [
                                    ['stringValue' => $imageUrl]
                                ]
                            ]
                        ];
                        Log::info('Image uploaded successfully', ['url' => $imageUrl]);
                    }
                } catch (\Exception $e) {
                    Log::error('Image upload failed', [
                        'error' => $e->getMessage()
                    ]);
                    throw new \Exception('Failed to upload image: ' . $e->getMessage());
                }
            }

            // Update the updated_at timestamp
            $fields['updated_at'] = [
                'mapValue' => [
                    'fields' => [
                        '_seconds' => ['integerValue' => time()],
                        '_nanoseconds' => ['integerValue' => 0]
                    ]
                ]
            ];

            // Prepare update URL - no need for update mask as we're sending all fields
            $updateUrl = "https://firestore.googleapis.com/v1/projects/{$firestore->getProjectId()}/databases/(default)/documents/kayu/{$id}";
            
            Log::info('Sending update to Firestore', [
                'url' => $updateUrl,
                'fields' => $fields
            ]);

            // Make the update request
            $response = \Illuminate\Support\Facades\Http::withToken($firestore->getAccessToken())
                ->patch($updateUrl, [
                    'fields' => $fields
                ]);

            if (!$response->successful()) {
                Log::error('Firestore update failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Firestore update failed: ' . $response->body());
            }

            Log::info('Update successful', [
                'response' => $response->json()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data updated successfully',
                'data' => $response->json()
            ]);

        } catch (\Exception $e) {
            Log::error('Update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating data: ' . $e->getMessage()
            ], 500);
        }
    }
}
