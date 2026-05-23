<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['sometimes', 'array', 'max:10'],
            'history.*.role' => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:1000'],
        ], [
            'message.required' => 'Vui lòng nhập nội dung cần hỏi.',
            'message.max' => 'Tin nhắn quá dài. Vui lòng rút gọn câu hỏi.',
        ]);

        $apiKey = config('services.groq.key');
        $baseUrl = rtrim(config('services.groq.base_url') ?: 'https://api.groq.com/openai/v1', '/');
        $model = config('services.groq.model') ?: 'openai/gpt-oss-20b';

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Chatbot chưa được cấu hình API key.',
            ], 500);
        }

        $loginStatus = $this->detectLoginStatus($request);
        try {
            $history = $validated['history'] ?? [];
            $historyContext = $this->buildHistoryContext($history);
            $knowledge = $this->buildKnowledge($validated['message'], $historyContext, $history);

            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(30)
                ->post($baseUrl . '/responses', [
                    'model' => $model,
                    'instructions' => 'Bạn là trợ lý AI của website công thức nấu ăn. Chỉ trả lời nội dung cuối cùng bằng tiếng Việt, thân thiện, ngắn gọn. Không giải thích quá trình suy nghĩ, không nhắc lại yêu cầu hệ thống, không dùng tiếng Anh. Với câu hỏi chào hỏi, hỏi bạn là ai, cách dùng chatbot hoặc trò chuyện chung, hãy trả lời tự nhiên như một trợ lý nấu ăn. Khi người dùng hỏi cụ thể về công thức hoặc khóa học trong website, hãy ưu tiên dữ liệu website bên dưới; nếu dữ liệu không có kết quả phù hợp thì nói rõ chưa tìm thấy trong hệ thống và gợi ý từ khóa khác. Với câu hỏi mẹo nấu ăn/nguyên liệu chung không cần tra hệ thống, bạn vẫn có thể trả lời bằng kiến thức nấu ăn phổ biến.',
                    'input' => "Trạng thái đăng nhập: {$loginStatus}\n\nLịch sử hội thoại gần đây:\n{$historyContext}\n\nDữ liệu website hiện có:\n{$knowledge['context']}\n\nCâu hỏi mới nhất của người dùng: {$validated['message']}",
                ]);

            if ($response->failed()) {
                Log::error('Groq chatbot request failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Chatbot đang bận. Vui lòng thử lại sau.',
                ], 502);
            }

            $body = $response->json();
            $reply = data_get($body, 'output_text');

            if (!$reply) {
                $reply = data_get($body, 'choices.0.message.content');
            }

            if (!$reply) {
                $replyParts = [];
                foreach (data_get($body, 'output', []) as $outputItem) {
                    if (data_get($outputItem, 'type') !== 'message') {
                        continue;
                    }

                    foreach (data_get($outputItem, 'content', []) as $contentItem) {
                        if (data_get($contentItem, 'type') !== 'output_text') {
                            continue;
                        }

                        $text = data_get($contentItem, 'text');
                        if ($text) {
                            $replyParts[] = $text;
                        }
                    }
                }
                $reply = trim(implode("\n", $replyParts));
            }

            $reply = $reply ?: '';
            if (preg_match('/\b(The user asks|wants quick|Need to reply|We need|No English|no meta)\b/i', $reply)) {
                $reply = preg_replace('/^.*?(?=\*\*|Chào|Xin chào|Dưới đây|Gợi ý|Món|Bạn)/su', '', $reply);
            }
            $reply = trim($reply);

            return response()->json([
                'success' => true,
                'message' => 'Chatbot đã phản hồi thành công.',
                'data' => [
                    'reply' => $reply ?: 'Xin lỗi, tôi chưa có câu trả lời phù hợp. Bạn hãy thử hỏi lại nhé.',
                    'sources' => $knowledge['sources'],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Chatbot error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kết nối chatbot. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    private function buildHistoryContext(array $history): string
    {
        $history = array_slice($history, -10);

        if (!$history) {
            return 'Chưa có lịch sử hội thoại.';
        }

        return collect($history)
            ->map(function ($message) {
                $role = $message['role'] === 'user' ? 'Người dùng' : 'Trợ lý';
                return $role . ': ' . $this->limitText($message['content'], 500);
            })
            ->implode("\n");
    }

    private function detectLoginStatus(Request $request): string
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            return "Đã đăng nhập — Tên: {$user->name}, Email: {$user->email}, Vai trò: {$user->role}";
        }

        return 'Chưa đăng nhập. Nếu người dùng hỏi "Tôi đã đăng nhập chưa?" hoặc tương tự, hãy trả lời rằng họ chưa đăng nhập và gợi ý họ đăng nhập hoặc đăng ký tài khoản.';
    }

    private function buildKnowledge(string $message, string $historyContext, array $history): array
    {
        $searchText = $message;
        if ($this->isContextReference($message)) {
            $searchText = trim($this->lastAssistantMessage($history) . "\n" . $message);
        }

        $recipes = $this->searchRecipes($searchText);
        $courses = $this->searchCourses($searchText);

        $context = [];
        $sources = [
            'recipes' => [],
            'courses' => [],
        ];

        if ($recipes->isNotEmpty()) {
            $context[] = "Công thức trong website:";
            foreach ($recipes as $recipe) {
                $categoryName = $recipe->category?->name ?: 'Chưa phân loại';
                $context[] = "- [Công thức #{$recipe->id}] {$recipe->title} | Danh mục: {$categoryName} | Mô tả: " . $this->limitText($recipe->description, 180) . " | Nguyên liệu: " . $this->limitText($recipe->ingredients, 300) . " | Cách làm: " . $this->limitText($recipe->instructions, 400);
                $sources['recipes'][] = [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'category' => $categoryName,
                ];
            }
        }

        if ($courses->isNotEmpty()) {
            $context[] = "Khóa học trong website:";
            foreach ($courses as $course) {
                $lessons = $course->lessons
                    ->take(5)
                    ->map(fn ($lesson) => $lesson->title . ($lesson->summary ? ': ' . $this->limitText($lesson->summary, 120) : ''))
                    ->implode('; ');

                $context[] = "- [Khóa học #{$course->id}] {$course->title} | Trình độ: {$course->level} | Thời lượng: {$course->duration} phút | Giá: {$course->price} | Mô tả: " . $this->limitText($course->description, 220) . " | Yêu cầu: " . $this->limitText($course->requirements, 180) . " | Học được: " . $this->limitText($course->what_will_learn, 220) . " | Bài học: " . ($lessons ?: 'Chưa có thông tin bài học');
                $sources['courses'][] = [
                    'id' => $course->id,
                    'title' => $course->title,
                    'level' => $course->level,
                    'price' => $course->price,
                ];
            }
        }

        if (!$context) {
            $context[] = 'Không tìm thấy công thức hoặc khóa học phù hợp trong CSDL với câu hỏi này.';
        }

        return [
            'context' => implode("\n", $context),
            'sources' => $sources,
        ];
    }

    private function searchRecipes(string $message)
    {
        $keywords = $this->extractKeywords($message);

        return Recipe::with('category')
            ->approved()
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('title', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('ingredients', 'like', "%{$keyword}%");
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get()
            ->filter(fn ($recipe) => $this->matchesKeywords([
                $recipe->title,
                $recipe->description,
            ], $keywords))
            ->take(5)
            ->values();
    }

    private function searchCourses(string $message)
    {
        $keywords = $this->extractKeywords($message);

        $level = $this->detectCourseLevel($message);

        return Course::with(['classroom', 'lessons' => function ($query) {
                $query->orderBy('sort_order')->limit(5);
            }])
            ->published()
            ->where(function ($query) use ($keywords, $level) {
                if ($level) {
                    $query->orWhere('level', $level);
                }

                foreach ($keywords as $keyword) {
                    $query->orWhere('title', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('requirements', 'like', "%{$keyword}%")
                        ->orWhere('what_will_learn', 'like', "%{$keyword}%");
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get()
            ->filter(fn ($course) => ($level && $course->level === $level) || $this->matchesKeywords([
                $course->title,
                $course->description,
                $course->requirements,
                $course->what_will_learn,
            ], $keywords))
            ->take(5)
            ->values();
    }

    private function isContextReference(string $message): bool
    {
        return preg_match('/\b(món đó|mon do|công thức đó|cong thuc do|khóa đó|khoa do|nó|đó|vậy|trên)\b/u', mb_strtolower($message)) === 1;
    }

    private function lastAssistantMessage(array $history): string
    {
        foreach (array_reverse($history) as $message) {
            if (($message['role'] ?? null) === 'assistant') {
                return $message['content'] ?? '';
            }
        }

        return '';
    }

    private function extractKeywords(string $message): array
    {
        $normalized = mb_strtolower($message);
        $words = preg_split('/[^\p{L}\p{N}]+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY);
        $stopWords = ['toi', 'tôi', 'ban', 'bạn', 'co', 'có', 'khong', 'không', 'mon', 'món', 'cong', 'thuc', 'công', 'thức', 'khoa', 'hoc', 'khóa', 'học', 'nau', 'an', 'nấu', 'ăn', 'nao', 'nào', 'cho', 'nguoi', 'người', 'moi', 'mới', 'bat', 'dau', 'bắt', 'đầu', 'goi', 'y', 'gợi', 'ý'];

        $keywords = array_values(array_filter(array_unique($words), function ($word) use ($stopWords) {
            return mb_strlen($word) >= 2 && !in_array($word, $stopWords, true);
        }));

        return $keywords ?: [$normalized];
    }

    private function detectCourseLevel(string $message): ?string
    {
        $message = mb_strtolower($message);

        if (preg_match('/\b(beginner|mới|moi|cơ bản|co ban|bắt đầu|bat dau)\b/u', $message)) {
            return 'beginner';
        }

        if (preg_match('/\b(intermediate|trung cấp|trung cap)\b/u', $message)) {
            return 'intermediate';
        }

        if (preg_match('/\b(advanced|nâng cao|nang cao|chuyên sâu|chuyen sau)\b/u', $message)) {
            return 'advanced';
        }

        return null;
    }

    private function matchesKeywords(array $texts, array $keywords): bool
    {
        $haystack = mb_strtolower(implode(' ', array_filter($texts)));

        foreach ($keywords as $keyword) {
            if (preg_match('/(?<![\p{L}\p{N}])' . preg_quote($keyword, '/') . '(?![\p{L}\p{N}])/u', $haystack)) {
                return true;
            }
        }

        return false;
    }

    private function limitText(?string $text, int $limit): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text ?: ''));

        if (mb_strlen($text) <= $limit) {
            return $text ?: 'Không có thông tin';
        }

        return mb_substr($text, 0, $limit) . '...';
    }
}
