<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Google\Auth\OAuth2;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Storage;
use Log;

class FirestoreService
{
    protected $accessToken;
    protected $projectId;

    public function __construct()
    {
        $jsonKey = storage_path('app/firebase-key.json');
        $this->projectId = config('app.firebase_project_id', env('FIREBASE_PROJECT_ID'));

        // Get Google OAuth2 token from service account
        $scopes = ['https://www.googleapis.com/auth/datastore'];

        $credentials = new ServiceAccountCredentials($scopes, $jsonKey);
        $this->accessToken = $credentials->fetchAuthToken()['access_token'];
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

            $response = Http::withToken($this->accessToken)
                ->post($url, $structuredQuery);

            return $this->processRunQueryResponse($response->json());
        }

        // Use standard get for non-filtered queries
        $response = Http::withToken($this->accessToken)
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

            $response = Http::withToken($this->accessToken)->get($url);

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

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function createDocument($collection, $data)
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}";

        $payload = [
            'fields' => $this->formatFields($data),
        ];

        $response = Http::withToken($this->accessToken)->post($url, $payload);

        return $response->json();
    }

    protected function formatFields($data)
    {
        $formatted = [];
        foreach ($data as $key => $value) {
            $formatted[$key] = ['stringValue' => (string) $value]; // ubah sesuai tipe (stringValue, integerValue, dll)
        }
        return $formatted;
    }
}
