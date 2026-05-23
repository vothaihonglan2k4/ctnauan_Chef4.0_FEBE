<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ManagerContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query();
        
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $contacts = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return response()->json([
            'contacts' => $contacts->items(),
            'pagination' => [
                'current_page' => $contacts->currentPage(),
                'last_page' => $contacts->lastPage(),
                'total' => $contacts->total()
            ],
            'stats' => [
                'total' => Contact::count(),
                'new' => Contact::where('status', 'new')->count(),
                'read' => Contact::where('status', 'read')->count(),
            ]
        ]);
    }

    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        
        // Mark as read
        if ($contact->status === 'new') {
            $contact->update(['status' => 'read']);
        }
        
        return response()->json(['contact' => $contact]);
    }

    public function updateStatus(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['status' => $request->status]);
        
        return response()->json([
            'message' => 'Cập nhật trạng thái thành công',
            'contact' => $contact
        ]);
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        
        return response()->json(['message' => 'Xóa liên hệ thành công']);
    }
}
