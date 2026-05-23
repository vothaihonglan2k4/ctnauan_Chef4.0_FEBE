<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClassroomRequest;
use App\Models\Classroom;
use Illuminate\Http\Request;

class AdminClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::withCount('courses')->orderBy('created_at', 'desc')->get();
        return response()->json(['classrooms' => $classrooms]);
    }

    public function store(StoreClassroomRequest $request)
    {
        $classroom = Classroom::create($request->only(['name', 'description', 'capacity', 'location', 'active']));
        return response()->json(['message' => 'Thêm phòng học thành công', 'classroom' => $classroom], 201);
    }

    public function update(StoreClassroomRequest $request, $id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->update($request->only(['name', 'description', 'capacity', 'location', 'active']));
        return response()->json(['message' => 'Cập nhật phòng học thành công', 'classroom' => $classroom]);
    }

    public function destroy($id)
    {
        $classroom = Classroom::withCount('courses')->findOrFail($id);
        if ($classroom->courses_count > 0) {
            return response()->json(['message' => 'Không thể xóa phòng học đang có khóa học'], 400);
        }
        $classroom->delete();
        return response()->json(['message' => 'Xóa phòng học thành công']);
    }
}
