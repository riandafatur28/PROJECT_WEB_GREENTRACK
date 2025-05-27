<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Google\Auth\OAuth2;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Storage\StorageClient;

class FirestoreService
{
    private $projectId;
    private $baseUrl = 'https://firestore.googleapis.com/v1';
    private $keyFile;

    public function __construct()
    {
        $this->projectId = 'green-track-firebase';
        $this->keyFile = storage_path('app/firebase-key.json');
    }

    public function getAccessToken()
    {
        $credentials = json_decode(file_get_contents($this->keyFile), true);

        $response = Http::post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->createJWT($credentials)
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get access token');
        }

        return $response->json()['access_token'];
    }

    private function createJWT($credentials)
    {
        $now = time();
        $exp = $now + 3600;

        $header = base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
            'kid' => $credentials['private_key_id']
        ]));

        $payload = base64_encode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $exp,
            'iat' => $now
        ]));

        $signature = '';
        openssl_sign(
            "$header.$payload",
            $signature,
            $credentials['private_key'],
            'SHA256'
        );
        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }

    /**
     * Get a collection of documents with optional filtering and pagination
     *
     * @param string $collection Collection name
     * @param array $options Optional parameters (filter, pageSize, pageToken)
     * @return array Collection data
     */
    public function getCollection($collection, $options = [])
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}";

        $query = [];

        // Add page size if provided
        if (isset($options['pageSize'])) {
            $query['pageSize'] = $options['pageSize'];
        }

        // Add page token if provided
        if (isset($options['pageToken'])) {
            $query['pageToken'] = $options['pageToken'];
        }

        // Add structured query for filtering if provided
        if (isset($options['filter']) && !empty($options['filter'])) {
            $structuredQuery = [
                'structuredQuery' => [
                    'from' => [['collectionId' => $collection]],
                    'where' => ['compositeFilter' => [
                        'op' => 'AND',
                        'filters' => [$options['filter']]
                    ]]
                ]
            ];

            // Use runQuery for filtering
            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";

            $response = Http::withToken($this->getAccessToken())
                ->post($url, $structuredQuery);

            return $this->processRunQueryResponse($response->json());
        }

        // Use standard get for non-filtered queries
        $response = Http::withToken($this->getAccessToken())
            ->get($url, $query);

        return $response->json();
    }

    /**
     * Process response from runQuery to match format of getCollection
     */
    private function processRunQueryResponse($queryResults)
    {
        $documents = [];
        $nextPageToken = null;

        foreach ($queryResults as $result) {
            if (isset($result['document'])) {
                $documents[] = $result['document'];
            }

            if (isset($result['readTime'])) {
                $readTime = $result['readTime'];
            }
        }

        return [
            'documents' => $documents,
            'nextPageToken' => $nextPageToken
        ];
    }

    /**
     * Get a single document from Firestore
     *
     * @param string $documentPath Full path to the document (e.g., 'collection/docId')
     * @return array|null Document data or null if not found
     */
    public function getDocument($documentPath)
    {
        try {
            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$documentPath}";

            $response = Http::withToken($this->getAccessToken())->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            // Log::error('Firestore getDocument error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            // Log::error('Error in getDocument: ' . $e->getMessage());
            return null;
        }
    }

    public function getProjectId()
    {
        return $this->projectId;
    }

public function createDocument($collection, $data)
{
    // Menyusun payload untuk Firestore
    $payload = [
        'fields' => []
    ];

    // Menambahkan data ke dalam 'fields' sesuai dengan skema yang diinginkan
    foreach ($data as $key => $value) {
        $payload['fields'][$key] = [
            'stringValue' => $value
        ];
    }

    // Menentukan URL untuk Firestore API
    $url = "{$this->baseUrl}/projects/{$this->projectId}/databases/(default)/documents/{$collection}";

    try {
        // Melakukan permintaan POST ke Firestore untuk membuat dokumen
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        // Memeriksa apakah respons berhasil
        if (!$response->successful()) {
            $errorMessage = $response->json()['error']['message'] ?? 'Unknown error';
            Log::error('Failed to create Firestore document', [
                'url' => $url,
                'status_code' => $response->status(),
                'error_message' => $errorMessage,
                'data_sent' => $data,
            ]);
            throw new \Exception('Failed to create document: ' . $errorMessage);
        }

        return $response->json();
    } catch (\Exception $e) {
        Log::error('Error creating Firestore document', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'data_sent' => $data,
        ]);
        throw $e;
    }
}


    public function createDocumentWithId($collection, $documentId, $data)
    {
        $url = "{$this->baseUrl}/projects/{$this->projectId}/databases/(default)/documents/{$collection}?documentId={$documentId}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        if (!$response->successful()) {
            throw new \Exception('Failed to create document: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Update an existing document
     *
     * @param string $collection Collection name
     * @param string $documentId Document ID or path
     * @param array $data Data to update (already in Firestore format with fields)
     * @return array Updated document data
     */
    public function updateDocument($collection, $documentId, $data)
    {
        // Extract just the document ID if a full path is provided
        $documentPath = str_contains($documentId, '/') ? $documentId : "{$collection}/{$documentId}";

        $url = "{$this->baseUrl}/projects/{$this->projectId}/databases/(default)/documents/{$documentPath}";

        // Data harus sudah dalam format Firestore fields, jadi kita tidak perlu konversi lagi
        // Contoh format: ['fields' => ['key' => ['stringValue' => 'value']]]

        // Use PATCH request
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ])->patch($url, $data);

        if (!$response->successful()) {
            throw new \Exception('Failed to update document: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Helper to convert PHP array to Firestore array value format
     */
    private function convertArrayToFirestore($array)
    {
        $result = [];
        foreach ($array as $value) {
            if (is_string($value)) {
                $result[] = ['stringValue' => $value];
            } elseif (is_int($value)) {
                $result[] = ['integerValue' => $value];
            } elseif (is_float($value)) {
                $result[] = ['doubleValue' => $value];
            } elseif (is_bool($value)) {
                $result[] = ['booleanValue' => $value];
            } elseif (is_null($value)) {
                $result[] = ['nullValue' => null];
            } elseif (is_array($value) && !$this->isAssoc($value)) {
                $result[] = ['arrayValue' => ['values' => $this->convertArrayToFirestore($value)]];
            } elseif (is_array($value)) {
                $result[] = ['mapValue' => ['fields' => $this->convertAssocArrayToFirestore($value)]];
            }
        }
        return $result;
    }

    /**
     * Helper to convert associative array to Firestore map format
     */
    private function convertAssocArrayToFirestore($array)
    {
        $fields = [];
        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $fields[$key] = ['stringValue' => $value];
            } elseif (is_int($value)) {
                $fields[$key] = ['integerValue' => $value];
            } elseif (is_float($value)) {
                $fields[$key] = ['doubleValue' => $value];
            } elseif (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } elseif (is_null($value)) {
                $fields[$key] = ['nullValue' => null];
            } elseif (is_array($value) && !$this->isAssoc($value)) {
                $fields[$key] = ['arrayValue' => ['values' => $this->convertArrayToFirestore($value)]];
            } elseif (is_array($value)) {
                $fields[$key] = ['mapValue' => ['fields' => $this->convertAssocArrayToFirestore($value)]];
            }
        }
        return $fields;
    }

    /**
     * Check if array is associative
     */
    private function isAssoc(array $array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}


