<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class HistoryBarcodeController extends Controller
{
    public function index(Request $request, FirestoreService $firestore)
{
    // Set Carbon locale to Bahasa Indonesia
    Carbon::setLocale('id');

    $search = $request->input('search', '');
    $searchType = $request->input('search_type', 'description'); // Get search type
    $page = $request->input('page', 1); // Requested page
    $perPage = 10; // Number of records per page

    // Prepare filter if there is a search
    $filter = null;
    if (!empty($search)) {
        if ($searchType == 'description') {
            $filter = [
                'fieldFilter' => [
                    'field' => ['fieldPath' => 'description'],
                    'op' => 'ARRAY_CONTAINS',
                    'value' => ['stringValue' => $search]
                ]
            ];
        } elseif ($searchType == 'jenis_aktivitas') {
            $filter = [
                'fieldFilter' => [
                    'field' => ['fieldPath' => 'activityType'], // Field activityType
                    'op' => 'ARRAY_CONTAINS',
                    'value' => ['stringValue' => $search]
                ]
            ];
        }
    }

    // Get the nextPageToken from session if available
    $pageToken = $request->session()->get('nextPageToken', null);

    // Get data from Firestore with pagination
    $response = $firestore->getCollection('activities', [
        'filter' => $filter,
        'pageSize' => $perPage, // Limit records per page
        'pageToken' => $pageToken, // Pass the pageToken from session
    ]);

    $activities = [];
    $total = 0;
    $nextPageToken = null;

    if (isset($response['documents'])) {
        foreach ($response['documents'] as $document) {
            $fields = $document['fields'] ?? [];
            $id = basename($document['name']);

            // Get timestamp for sorting
            $timestamp = $this->getTimestampValue($fields);

            // Get activity type from activityType
            $activityType = isset($fields['activityType']['stringValue'])
                ? $fields['activityType']['stringValue']
                : '-';

            // Get description
            $description = isset($fields['description']['stringValue'])
                ? $fields['description']['stringValue']
                : 'No description';

            // Parse userRole
            $userRole = isset($fields['userRole']['stringValue'])
                ? $this->parseRole($fields['userRole']['stringValue'])
                : 'No role';

            // Parse the date using Carbon
            $carbonDate = Carbon::createFromTimestamp($timestamp);

            $activities[] = [
                'id' => $id,
                'nama' => $fields['userName']['stringValue'] ?? '-',
                'userRole' => $userRole,
                'keterangan' => $activityType,
                'detail' => $description,
                'waktu' => $carbonDate->toIso8601String(), // ISO8601 format
                'timestamp' => $timestamp // For sorting
            ];

            $total++;
        }

        // Sort by the most recent timestamp
        usort($activities, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        // Get nextPageToken if available for pagination
        $nextPageToken = $response['nextPageToken'] ?? null;
        if ($nextPageToken) {
            // Store nextPageToken in session
            $request->session()->put('nextPageToken', $nextPageToken);
        }
    }

    // Calculate total pages based on total data
    $totalPages = ceil($total / $perPage);

    return view('layouts.historyscan', [
        'activities' => $activities,
        'total' => $total,
        'currentPage' => $page,
        'perPage' => $perPage,
        'search' => $search,
        'searchType' => $searchType,
        'nextPageToken' => $nextPageToken,  // Token for next page
        'totalPages' => $totalPages,  // Total pages for pagination
    ]);
}


    // Function to get timestamp value from various formats for sorting
    private function getTimestampValue($fields)
    {
        if (isset($fields['timestamp']['timestampValue'])) {
            return strtotime($fields['timestamp']['timestampValue']);
        }
        if (isset($fields['createdAt']['timestampValue'])) {
            return strtotime($fields['createdAt']['timestampValue']);
        }
        if (isset($fields['tanggal']['timestampValue'])) {
            return strtotime($fields['tanggal']['timestampValue']);
        }

        return time();
    }

    // Function to parse role into a more user-friendly format
    private function parseRole($roleString)
    {
        $roleString = str_replace('UserRole.', '', $roleString);
        $roleString = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $roleString); // camelCase to spaces
        $roleString = str_replace('_', ' ', $roleString); // snake_case to spaces

        return ucwords($roleString);
    }
}
