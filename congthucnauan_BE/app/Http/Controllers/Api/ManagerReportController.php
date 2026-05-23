<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManagerReportController extends Controller
{
    public function overview()
    {
        try {
            $stats = [
                'recipes' => [
                    'total' => Recipe::count(),
                    'approved' => Recipe::where('status', 'approved')->count(),
                    'pending' => Recipe::where('status', 'pending')->count(),
                    'rejected' => Recipe::where('status', 'rejected')->count(),
                ],
                'courses' => [
                    'total' => Course::count(),
                    'published' => Course::where('status', 'published')->count(),
                    'draft' => Course::where('status', 'draft')->count(),
                ],
                'users' => [
                    'total' => CourseEnrollment::distinct('user_id')->count('user_id'),
                    'this_month' => CourseEnrollment::whereNotNull('enrollment_date')
                        ->whereMonth('enrollment_date', now()->month)
                        ->whereYear('enrollment_date', now()->year)
                        ->distinct('user_id')
                        ->count('user_id'),
                ],
                'revenue' => [
                    'total' => (float) Payment::where('status', 'completed')->sum('amount'),
                    'this_month' => (float) Payment::where('status', 'completed')
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->sum('amount'),
                ],
            ];

            return response()->json(['stats' => $stats]);
        } catch (\Exception $e) {
            Log::error('Manager Report Error: ' . $e->getMessage());
            return response()->json([
                'stats' => [
                    'recipes' => ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0],
                    'courses' => ['total' => 0, 'published' => 0, 'draft' => 0],
                    'users' => ['total' => 0, 'this_month' => 0],
                    'revenue' => ['total' => 0, 'this_month' => 0],
                ],
                'error' => $e->getMessage()
            ]);
        }
    }
}
