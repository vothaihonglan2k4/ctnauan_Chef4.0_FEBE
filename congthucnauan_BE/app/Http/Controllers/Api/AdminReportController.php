<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function overview()
    {
        $stats = [
            'users' => User::count(),
            'recipes' => Recipe::count(),
            'categories' => Category::count(),
            'payments' => Payment::count(),
            'courses' => Course::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'new_users_30_days' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'new_recipes_30_days' => Recipe::where('created_at', '>=', now()->subDays(30))->count(),
        ];
        
        return response()->json(['stats' => $stats]);
    }

    public function recipesByCategory()
    {
        $data = Category::withCount('recipes')
            ->orderBy('recipes_count', 'desc')
            ->get()
            ->map(fn($c) => ['name' => $c->name, 'count' => $c->recipes_count]);
        
        return response()->json(['data' => $data]);
    }

    public function revenueByMonth()
    {
        $data = Payment::where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return response()->json(['data' => $data]);
    }

    public function userRegistrations()
    {
        $data = User::where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return response()->json(['data' => $data]);
    }

    public function paymentsByMethod()
    {
        $data = Payment::where('status', 'completed')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();
        
        return response()->json(['data' => $data]);
    }

    public function popularRecipes()
    {
        $recipes = Recipe::with(['category', 'user'])
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->orderByDesc('ratings_avg_rating')
            ->orderByDesc('ratings_count')
            ->limit(10)
            ->get();
        
        return response()->json(['recipes' => $recipes]);
    }

    public function topCourses()
    {
        $courses = Course::with('classroom')
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit(10)
            ->get();
        
        return response()->json(['courses' => $courses]);
    }
}
