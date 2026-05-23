<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        try {
            // Get total counts
            $userCount = User::count();
            
            // Get users created this month
            $userCountThisMonth = User::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->count();
            
            $recipeCount = Recipe::count();
            $courseCount = Course::count();
            
            // Get total revenue from completed payments
            $totalRevenue = Payment::where('status', 'completed')
                ->sum('amount');
            
            return response()->json([
                'userCount' => $userCount,
                'userCountThisMonth' => $userCountThisMonth,
                'recipeCount' => $recipeCount,
                'courseCount' => $courseCount,
                'totalRevenue' => $totalRevenue
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy thống kê',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get recent users
     */
    public function getRecentUsers(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            
            $users = User::select('id', 'name', 'email', 'role', 'avatar', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
            
            return response()->json([
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get recent payments
     */
    public function getRecentPayments(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            
            $payments = Payment::with(['user', 'enrollment.course'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'status' => $payment->status,
                        'created_at' => $payment->created_at,
                        'user_name' => $payment->user ? $payment->user->name : 'N/A',
                        'course_title' => $payment->enrollment && $payment->enrollment->course 
                            ? $payment->enrollment->course->title 
                            : 'N/A'
                    ];
                });
            
            return response()->json([
                'payments' => $payments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách thanh toán',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get recent courses
     */
    public function getRecentCourses(Request $request)
    {
        try {
            $limit = $request->query('limit', 6);
            
            $courses = Course::select(
                    'courses.id',
                    'courses.title',
                    'courses.description',
                    'courses.image',
                    'courses.price',
                    'courses.created_at',
                    'classrooms.name as classroom_name'
                )
                ->leftJoin('classrooms', 'courses.classroom_id', '=', 'classrooms.id')
                ->orderBy('courses.created_at', 'desc')
                ->limit($limit)
                ->get();
            
            return response()->json([
                'courses' => $courses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy danh sách khóa học',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
