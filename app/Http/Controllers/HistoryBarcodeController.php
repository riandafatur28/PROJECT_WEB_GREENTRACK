<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Log;

class HistoryBarcodeController extends Controller
{
    public function index(Request $request, FirestoreService $firestore)
    {
        // Set Carbon locale to Bahasa Indonesia
        Carbon::setLocale('id');

        $search = $request->input('search', '');
        $searchType = $request->input('search_type', 'description'); // Get search type
        $sortOrder = $request->input('sort', 'terbaru'); // Get sort order (terbaru/terlama)
        $page = (int) $request->input('page', 1); // Requested page
        $perPage = 10; // Number of records per page

        // Prepare filter if there is a search
        $filter = null;
        if (!empty($search)) {
            if ($searchType == 'description') {
                $filter = [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => 'description'],
                        'op' => 'EQUAL', // Changed from ARRAY_CONTAINS to EQUAL or CONTAINS
                        'value' => ['stringValue' => $search]
                    ]
                ];
            } elseif ($searchType == 'jenis_aktivitas') {
                $filter = [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => 'activityType'], // Field activityType
                        'op' => 'EQUAL', // Changed from ARRAY_CONTAINS to EQUAL
                        'value' => ['stringValue' => $search]
                    ]
                ];
            } elseif ($searchType == 'nama') {
                $filter = [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => 'userName'],
                        'op' => 'EQUAL',
                        'value' => ['stringValue' => $search]
                    ]
                ];
            }
        }

        // Prepare order by based on sort order
        $orderBy = [
            'field' => ['fieldPath' => 'timestamp'],
            'direction' => $sortOrder === 'terlama' ? 'ASCENDING' : 'DESCENDING'
        ];

        // Get all data first to calculate accurate pagination
        // For better performance, you might want to implement server-side counting
        $allResponse = $firestore->getCollection('activities', [
            'filter' => $filter,
            'orderBy' => [$orderBy],
        ]);

        $allActivities = [];
        $total = 0;

        // Create a cache for user photos to avoid multiple queries for the same user
        $userPhotoCache = [];

        if (isset($allResponse['documents'])) {
            foreach ($allResponse['documents'] as $document) {
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

                // Get user ID from the document
                $userId = $fields['userId']['stringValue'] ?? null;

                // Get user photo URL from akun collection
                $photoUrl = null;
                if ($userId) {
                    // Check if we already fetched this user's photo
                    if (!isset($userPhotoCache[$userId])) {
                        try {
                            // Query the akun collection to get the user's photo_url
                            $userDoc = $firestore->getDocument('akun/' . $userId);

                            if ($userDoc && isset($userDoc['fields']['photo_url']['stringValue'])) {
                                $userPhotoCache[$userId] = $userDoc['fields']['photo_url']['stringValue'];
                            } else {
                                $userPhotoCache[$userId] = null; // Cache null to avoid repeated queries
                            }
                        } catch (\Exception $e) {
                            // Log::error('Error fetching user photo: ' . $e->getMessage());
                            $userPhotoCache[$userId] = null;
                        }
                    }

                    $photoUrl = $userPhotoCache[$userId];
                }

                // Parse the date using Carbon
                $carbonDate = Carbon::createFromTimestamp($timestamp);

                $allActivities[] = [
                    'id' => $id,
                    'nama' => $fields['userName']['stringValue'] ?? '-',
                    'userRole' => $userRole,
                    'keterangan' => $activityType,
                    'detail' => $description,
                    'waktu' => $carbonDate->toIso8601String(), // ISO8601 format
                    'timestamp' => $timestamp, // For sorting
                    'imageUrl' => $photoUrl,  // Use the user's photo URL from akun collection
                ];

                $total++;
            }

            // Sort activities based on sort order
            if ($sortOrder === 'terlama') {
                usort($allActivities, function($a, $b) {
                    return $a['timestamp'] - $b['timestamp']; // Oldest first
                });
            } else {
                usort($allActivities, function($a, $b) {
                    return $b['timestamp'] - $a['timestamp']; // Newest first
                });
            }
        }

        // Calculate total pages based on total data
        $totalPages = ceil($total / $perPage);

        // Ensure page is within bounds
        $page = max(1, min($page, $totalPages));

        // Get activities for current page
        $offset = ($page - 1) * $perPage;
        $activities = array_slice($allActivities, $offset, $perPage);

        // Clear pagination token from session when search/sort changes
        if ($request->has('search') || $request->has('sort')) {
            $request->session()->forget('nextPageToken');
        }

        return view('layouts.historyscan', [
            'activities' => $activities,
            'total' => $total,
            'currentPage' => $page,
            'perPage' => $perPage,
            'search' => $search,
            'searchType' => $searchType,
            'sortOrder' => $sortOrder, // Added sort order
            'totalPages' => $totalPages,
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
