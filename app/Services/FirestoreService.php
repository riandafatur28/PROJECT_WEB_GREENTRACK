<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Google\Auth\OAuth2;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Storage;

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

    public function getCollection($collection)
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}";

        $response = Http::withToken($this->accessToken)->get($url);

        return $response->json();
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