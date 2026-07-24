<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ManagerCourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('classroom')
            ->withCount(['enrollments as student_count', 'lessons as lesson_count'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $stats = [
            'total' => $courses->count(),
            'published' => $courses->where('status', 'published')->count(),
            'draft' => $courses->where('status', 'draft')->count(),
            'total_students' => $courses->sum('student_count'),
        ];
        
        return response()->json([
            'courses' => $courses,
            'stats' => $stats
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'classroom_id' => 'required|exists:classrooms,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'status' => 'in:draft,published',
        ]);

        $data = $request->only(['title', 'description', 'classroom_id', 'price', 'duration', 'level', 'status']);
        $data['status'] = $data['status'] ?? 'draft';
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/courses'), $filename);
            $data['image'] = $filename;
        }

        $course = Course::create($data);
        
        return response()->json([
            'message' => 'Thêm khóa học thành công',
            'course' => $course->load('classroom')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'classroom_id' => 'required|exists:classrooms,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'status' => 'in:draft,published,archived',
        ]);

        $data = $request->only(['title', 'description', 'classroom_id', 'price', 'duration', 'level', 'status']);
        
        if ($request->hasFile('image')) {
            // Delete old image
            if ($course->image) {
                @unlink(public_path('uploads/courses/' . $course->image));
            }
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/courses'), $filename);
            $data['image'] = $filename;
        }

        $course->update($data);
        
        return response()->json([
            'message' => 'Cập nhật khóa học thành công',
            'course' => $course->load('classroom')
        ]);
    }

    public function destroy($id)
    {
        $course = Course::withCount('enrollments')->findOrFail($id);
        
        if ($course->enrollments_count > 0) {
            return response()->json([
                'message' => 'Không thể xóa khóa học đang có học viên'
            ], 400);
        }
        
        if ($course->image) {
            @unlink(public_path('uploads/courses/' . $course->image));
        }
        
        $course->delete();
        
        return response()->json(['message' => 'Xóa khóa học thành công']);
    }

    public function updateStatus(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:draft,published,archived'
        ]);
        
        $course->update(['status' => $request->status]);
        
        $statusText = [
            'published' => 'xuất bản',
            'draft' => 'chuyển về nháp',
            'archived' => 'lưu trữ'
        ];
        
        return response()->json([
            'message' => "Khóa học đã được {$statusText[$request->status]}",
            'course' => $course
        ]);
    }

    public function classrooms()
    {
        return response()->json([
            'classrooms' => Classroom::where('active', true)->get()
        ]);
    }

    // ========== LESSON MANAGEMENT ==========

    /**
     * Get all lessons for a specific course
     */
    public function getLessons($courseId)
    {
        $course = Course::findOrFail($courseId);
        $lessons = CourseLesson::where('course_id', $courseId)
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'course' => $course,
            'lessons' => $lessons
        ]);
    }

    /**
     * Create a new lesson
     */
    public function storeLesson(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url|max:255',
            'duration_minutes' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_free' => 'nullable|boolean',
            'summary' => 'nullable|string',
            'image' => 'nullable|string|max:255',
        ]);

        $data = $request->only([
            'title', 'content', 'video_url', 'duration_minutes',
            'sort_order', 'is_free', 'summary', 'image'
        ]);
        $data['course_id'] = $courseId;
        $data['is_free'] = $data['is_free'] ?? false;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['duration_minutes'] = $data['duration_minutes'] ?? 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/lessons'), $filename);
            $data['image'] = $filename;
        } elseif (!isset($data['image'])) {
            $data['image'] = 'no-image.jpg';
        }

        $lesson = CourseLesson::create($data);

        return response()->json([
            'message' => 'Thêm bài học thành công',
            'lesson' => $lesson
        ], 201);
    }

    /**
     * Update a lesson
     */
    public function updateLesson(Request $request, $courseId, $lessonId)
    {
        $lesson = CourseLesson::where('course_id', $courseId)->findOrFail($lessonId);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url|max:255',
            'duration_minutes' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_free' => 'nullable|boolean',
            'summary' => 'nullable|string',
            'image' => 'nullable|string|max:255',
        ]);

        $data = $request->only([
            'title', 'content', 'video_url', 'duration_minutes',
            'sort_order', 'is_free', 'summary', 'image'
        ]);

        // Convert boolean from form data (sends '1'/'0' as strings)
        if (isset($data['is_free'])) {
            $data['is_free'] = filter_var($data['is_free'], FILTER_VALIDATE_BOOLEAN);
        } else {
            unset($data['is_free']);
        }

        // Handle image upload (only if a new file was uploaded)
        if ($request->hasFile('image')) {
            if ($lesson->image && $lesson->image !== 'no-image.jpg') {
                @unlink(public_path('uploads/lessons/' . $lesson->image));
            }
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/lessons'), $filename);
            $data['image'] = $filename;
        } else {
            // No new image uploaded - keep the original image
            unset($data['image']);
        }

        $lesson->update($data);

        return response()->json([
            'message' => 'Cập nhật bài học thành công',
            'lesson' => $lesson
        ]);
    }

    /**
     * Delete a lesson
     */
    public function destroyLesson($courseId, $lessonId)
    {
        $lesson = CourseLesson::where('course_id', $courseId)->findOrFail($lessonId);

        if ($lesson->image && $lesson->image !== 'no-image.jpg') {
            @unlink(public_path('uploads/lessons/' . $lesson->image));
        }

        $lesson->delete();

        return response()->json(['message' => 'Xóa bài học thành công']);
    }

    /**
     * Update sort order of lessons (drag & drop reordering)
     */
    public function reorderLessons(Request $request, $courseId)
    {
        $request->validate([
            'lesson_ids' => 'required|array',
            'lesson_ids.*' => 'integer|exists:course_lessons,id',
        ]);

        foreach ($request->lesson_ids as $index => $lessonId) {
            CourseLesson::where('id', $lessonId)
                ->where('course_id', $courseId)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['message' => 'Cập nhật thứ tự bài học thành công']);
    }
}
