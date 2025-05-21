<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Google\Auth\OAuth2;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Storage;
use Log;
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
        $url = "{$this->baseUrl}/projects/{$this->projectId}/databases/(default)/documents/{$collection}";
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        if (!$response->successful()) {
            throw new \Exception('Failed to create document');
        }

        return $response->json();
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
}


