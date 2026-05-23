<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
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
}
