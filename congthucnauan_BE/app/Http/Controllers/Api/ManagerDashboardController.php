<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Contact;
use Illuminate\Http\Request;

class ManagerDashboardController extends Controller
{
    public function getStats()
    {
        return response()->json([
            'total_recipes' => Recipe::where('status', 'approved')->count(),
            'pending_recipes' => Recipe::where('status', 'pending')->count(),
            'total_courses' => Course::where('status', 'published')->count(),
            'total_students' => CourseEnrollment::where('status', 'active')->count(),
        ]);
    }

    public function getRecentContacts()
    {
        $contacts = Contact::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json(['contacts' => $contacts]);
    }

    public function getPendingRecipes()
    {
        $recipes = Recipe::with(['user', 'category'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json(['recipes' => $recipes]);
    }
}
