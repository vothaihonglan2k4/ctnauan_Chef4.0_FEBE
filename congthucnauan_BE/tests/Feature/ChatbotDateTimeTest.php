<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;

class ChatbotDateTimeTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_chatbot_returns_current_time_and_date_without_ai_api(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 24, 14, 30, 45, 'Asia/Ho_Chi_Minh'));
        config(['services.groq.key' => null]);

        $response = $this->postJson('/api/v1/chatbot', [
            'message' => 'Bây giờ là mấy giờ và hôm nay ngày mấy?',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.reply',
                'Hiện tại là 14:30:45, Thứ Sáu, ngày 24/07/2026 (giờ Việt Nam).'
            );
    }

    public function test_chatbot_returns_current_date_without_ai_api(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 24, 14, 30, 45, 'Asia/Ho_Chi_Minh'));
        config(['services.groq.key' => null]);

        $response = $this->postJson('/api/v1/chatbot', [
            'message' => 'Hôm nay là thứ mấy?',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.reply',
                'Hôm nay là Thứ Sáu, ngày 24/07/2026 (giờ Việt Nam).'
            );
    }
}
