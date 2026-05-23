<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseEnrollment;
use App\Models\CourseLessonCompletion;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Get all courses
     */
    public function index(Request $request)
    {
        $query = Course::with(['classroom'])
            ->withCount(['enrollments', 'lessons'])
            ->published();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 12);
        $courses = $query->paginate($perPage);

        return response()->json([
            'courses' => $courses->items(),
            'pagination' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total()
            ]
        ]);
    }

    /**
     * Get single course
     */
    public function show($id)
    {
        $course = Course::with(['classroom', 'lessons' => function($query) {
            $query->orderBy('sort_order');
        }])
        ->withCount('enrollments')
        ->findOrFail($id);

        $user = auth('sanctum')->user();
        $isEnrolled = false;
        $enrollment = null;

        if ($user) {
            $enrollment = CourseEnrollment::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->first();
            
            if ($enrollment) {
                $isEnrolled = true;
            }
        }

        return response()->json([
            'success' => true,
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'image' => $course->image,
                'level' => $course->level,
                'total_duration' => $course->duration,
                'price' => $course->price,
                'discount_price' => $course->discount_price,
                'max_students' => $course->max_students,
                'start_date' => $course->start_date,
                'end_date' => $course->end_date,
                'status' => $course->status,
                'enrollments_count' => $course->enrollments_count,
                'instructor_name' => 'Admin',
                'requirements' => $course->requirements,
                'what_you_will_learn' => $course->what_will_learn,
                'classroom' => $course->classroom ? [
                    'id' => $course->classroom->id,
                    'name' => $course->classroom->name,
                    'location' => $course->classroom->location,
                    'capacity' => $course->classroom->capacity
                ] : null,
            ],
            'lessons' => $course->lessons->map(function($lesson) use ($user) {
                $isCompleted = false;
                if ($user) {
                    $isCompleted = CourseLessonCompletion::where('user_id', $user->id)
                        ->where('lesson_id', $lesson->id)
                        ->exists();
                }
                
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'description' => $lesson->description,
                    'video_url' => $lesson->video_url,
                    'duration_minutes' => $lesson->duration_minutes,
                    'order' => $lesson->sort_order,
                    'is_free' => $lesson->is_free,
                    'is_completed' => $isCompleted
                ];
            }),
            'is_enrolled' => $isEnrolled,
            'enrollment' => $enrollment ? [
                'id' => $enrollment->id,
                'progress' => $enrollment->progress,
                'status' => $enrollment->status
            ] : null,
        ]);
    }

    /**
     * Enroll in course
     */
    public function enroll(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $user = $request->user();

        // Check if already enrolled
        $existingEnrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'message' => 'Bạn đã đăng ký khóa học này rồi'
            ], 400);
        }

        // Check if course is full
        if ($course->enrollments()->count() >= $course->max_students) {
            return response()->json([
                'message' => 'Khóa học đã đầy'
            ], 400);
        }

        // Check if payment is required
        if ($course->price > 0) {
            return response()->json([
                'message' => 'Vui lòng thanh toán để đăng ký khóa học',
                'requires_payment' => true,
                'price' => $course->discount_price ?? $course->price
            ], 402);
        }

        // Free course - enroll directly
        $enrollment = CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'status' => 'active',
            'progress' => 0,
            'enrollment_date' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký khóa học thành công',
            'enrollment' => $enrollment
        ], 201);
    }

    /**
     * Get my enrolled courses
     */
    public function myCourses(Request $request)
    {
        $enrollments = CourseEnrollment::with(['course.classroom', 'course.lessons'])
            ->where('user_id', $request->user()->id)
            ->orderBy('enrollment_date', 'desc')
            ->paginate(12);

        return response()->json([
            'success' => true,
            'enrollments' => $enrollments->map(function($enrollment) {
                return [
                    'id' => $enrollment->id,
                    'status' => $enrollment->status,
                    'progress' => $enrollment->progress,
                    'enrolled_at' => $enrollment->enrollment_date,
                    'course' => [
                        'id' => $enrollment->course->id,
                        'title' => $enrollment->course->title,
                        'description' => $enrollment->course->description,
                        'image' => $enrollment->course->image,
                        'level' => $enrollment->course->level,
                        'duration' => $enrollment->course->duration,
                        'lessons_count' => $enrollment->course->lessons->count()
                    ]
                ];
            }),
            'pagination' => [
                'current_page' => $enrollments->currentPage(),
                'last_page' => $enrollments->lastPage(),
                'per_page' => $enrollments->perPage(),
                'total' => $enrollments->total()
            ]
        ]);
    }

    /**
     * Get my course schedules (offline classes)
     */
    public function mySchedule(Request $request)
    {
        $enrollments = CourseEnrollment::with(['course.classroom'])
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->get();

        $schedules = $enrollments->map(function($enrollment) {
            $course = $enrollment->course;
            $classroom = $course->classroom;
            
            // Determine location - if no classroom, it's online
            $location = 'Trực tuyến';
            if ($classroom) {
                $location = $classroom->location ?? $classroom->name ?? 'Chưa xác định';
            }
            
            // Calculate next class date based on start_date
            $nextClass = 'Chưa xác định';
            if ($course->start_date) {
                $startDate = \Carbon\Carbon::parse($course->start_date);
                if ($startDate->isFuture()) {
                    $nextClass = $startDate->format('d/m/Y');
                } else {
                    $nextClass = 'Không xác định';
                }
            }
            
            return [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'location' => $location,
                'schedule_time' => $course->schedule ?? 'T2, T4, T6: 18:00-20:00',
                'instructor' => 'Admin',
                'next_class' => $nextClass,
                'start_date' => $course->start_date,
                'end_date' => $course->end_date
            ];
        })->values();

        return response()->json([
            'success' => true,
            'schedules' => $schedules
        ]);
    }

    /**
     * Mark lesson as completed
     */
    public function completeLesson(Request $request, $courseId, $lessonId)
    {
        $user = $request->user();
        
        // Check enrollment
        $enrollment = CourseEnrollment::where('course_id', $courseId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $lesson = CourseLesson::where('id', $lessonId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Mark as completed
        CourseLessonCompletion::firstOrCreate([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id
        ]);

        // Update enrollment progress
        $totalLessons = CourseLesson::where('course_id', $courseId)->count();
        $completedLessons = CourseLessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', function($query) use ($courseId) {
                $query->select('id')
                    ->from('course_lessons')
                    ->where('course_id', $courseId);
            })
            ->count();

        $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
        
        $enrollment->update([
            'progress' => $progress,
            'status' => $progress >= 100 ? 'completed' : 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã hoàn thành bài học',
            'progress' => $progress
        ]);
    }

    /**
     * Get lesson content (for enrolled users)
     */
    public function getLesson(Request $request, $courseId, $lessonId)
    {
        $user = $request->user();
        $lesson = CourseLesson::where('id', $lessonId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Check if lesson is free or user is enrolled
        if (!$lesson->is_free) {
            $enrollment = CourseEnrollment::where('course_id', $courseId)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'message' => 'Bạn cần đăng ký khóa học để xem bài học này'
                ], 403);
            }
        }

        $isCompleted = CourseLessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->exists();

        return response()->json([
            'success' => true,
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'description' => $lesson->description,
                'content' => $lesson->content,
                'video_url' => $lesson->video_url,
                'duration_minutes' => $lesson->duration_minutes,
                'order' => $lesson->sort_order,
                'is_completed' => $isCompleted
            ]
        ]);
    }
}