<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Models\Course;
use App\Models\Classroom;
use Illuminate\Http\Request;

class AdminCourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('classroom')->withCount('enrollments as student_count')->orderBy('created_at', 'desc')->get();
        return response()->json(['courses' => $courses]);
    }

    public function store(StoreCourseRequest $request)
    {
        $data = $request->only(['title', 'description', 'classroom_id', 'price', 'duration', 'status']);
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/courses'), $filename);
            $data['image'] = $filename;
        }

        $course = Course::create($data);
        return response()->json(['message' => 'Thêm khóa học thành công', 'course' => $course], 201);
    }

    public function update(StoreCourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        $data = $request->only(['title', 'description', 'classroom_id', 'price', 'duration', 'status']);
        
        if ($request->hasFile('image')) {
            if ($course->image) @unlink(public_path('uploads/courses/' . $course->image));
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/courses'), $filename);
            $data['image'] = $filename;
        }

        $course->update($data);
        return response()->json(['message' => 'Cập nhật khóa học thành công', 'course' => $course]);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        if ($course->image) @unlink(public_path('uploads/courses/' . $course->image));
        $course->delete();
        return response()->json(['message' => 'Xóa khóa học thành công']);
    }

    public function classrooms()
    {
        return response()->json(['classrooms' => Classroom::where('active', true)->get()]);
    }
}
