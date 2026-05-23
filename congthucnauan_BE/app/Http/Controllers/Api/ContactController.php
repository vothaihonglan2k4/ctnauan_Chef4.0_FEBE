<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\StoreContactRequest;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Handle contact form submission
     */
    public function submit(StoreContactRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Save to database (like old PHP MVC)
            $contact = \App\Models\Contact::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'subject' => $data['subject'],
                'message' => $data['message'],
                'status' => 'new'
            ]);

            // Also log for tracking
            Log::info('Contact form submission saved', [
                'contact_id' => $contact->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại sau.'
            ], 500);
        }
    }
}
