<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIChatController extends Controller
{
    /**
     * Handle AI chat requests
     */
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'nullable|array|max:10',
            'context' => 'nullable|string|max:200'
        ]);

        $userMessage = $validated['message'];
        $history = $validated['history'] ?? [];
        $pageContext = $validated['context'] ?? '';
        $llmMuxBaseUrl = env('LLM_MUX_BASE_URL', 'http://localhost:8317/v1');
        $llmMuxApiKey = env('LLM_MUX_API_KEY', 'unused');
        $llmMuxModel = env('LLM_MUX_MODEL', 'gemini-2.5-flash');

        try {
            $systemContext = $this->buildSystemContext();
            if ($pageContext) {
                $systemContext .= "\n\nNGỮ CẢNH HIỆN TẠI: Người dùng đang ở trang " . $pageContext . ". Ưu tiên trả lời về tính năng liên quan đến trang này.";
            }
            

            // --- Call LLM via llm-mux ---
            $llmError = null;
            if ($llmMuxBaseUrl) {
                $llmReply = $this->callLLM($llmMuxBaseUrl, $llmMuxApiKey, $llmMuxModel, $systemContext, $history, $userMessage);
                if ($llmReply['success']) {
                    return response()->json([
                        'success' => true,
                        'reply' => $llmReply['reply'],
                        'message' => null
                    ], 200);
                }
                $llmError = $llmReply['message'];
            }

            // Return error if LLM call failed
            $errorMsg = $llmError ?? 'AI không trả về kết quả phù hợp.';
            return response()->json([
                'success' => false,
                'reply' => null,
                'message' => $errorMsg
            ], 200);
        } catch (\Exception $e) {
            Log::error('AIChat error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'reply' => null,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 200);
        }
    }

    /**
     * Build system context for AI
     */
    private function buildSystemContext(): string
    {
         return "Bạn là trợ lý AI thông minh của Cloody - hệ thống quản lý file đám mây tiên tiến.\n\n" .
             "TÍNH NĂNG CHÍNH:\n" .
             "1. Quản lý File & Folder:\n" .
             "   - Upload nhiều file/folder cùng lúc (kéo thả hoặc nút Upload)\n" .
             "   - Tạo thư mục con không giới hạn, đổi tên, di chuyển\n" .
             "   - Xem trước file (ảnh, PDF, văn bản, video)\n" .
             "   - Tải xuống file hoặc cả folder\n" .
             "   - Sửa/xóa, tìm kiếm nhanh\n" .
             "   - Tùy chỉnh màu sắc thư mục\n\n" .
             "2. Chia sẻ:\n" .
             "   - Chia sẻ qua email hoặc link công khai\n" .
             "   - Đặt hạn chia sẻ, quyền truy cập (xem/tải/sửa)\n" .
             "   - Chia sẻ với nhóm làm việc\n\n" .
             "3. Nhóm (Groups):\n" .
             "   - Tạo nhóm riêng tư/công khai\n" .
             "   - Thêm thành viên, phân quyền admin/member\n" .
             "   - Chia sẻ file/folder trong nhóm\n" .
             "   - Khám phá, tham gia nhóm công khai\n\n" .
             "4. Tính năng nâng cao:\n" .
             "   - Yêu thích: Đánh dấu file/folder quan trọng\n" .
             "   - Gần đây: Xem file truy cập gần đây\n" .
             "   - Thùng rác: Khôi phục hoặc xóa vĩnh viễn\n" .
             "   - Xử lý trùng lặp: Replace/Merge/Skip tự động\n\n" .
             "5. Gói lưu trữ:\n" .
             "   - Free: 5GB | Basic: 50GB | Premium: 200GB | Enterprise: 1TB\n" .
             "   - Thanh toán qua VNPay an toàn\n\n" .
             "HƯỚNG DẪN SỬ DỤNG (step-by-step):\n" .
             "- Khi người dùng hỏi về thao tác, hãy trả lời từng bước rõ ràng, ví dụ:\n" .
             "  Bước 1: Nhấn nút Upload ở góc phải\n" .
             "  Bước 2: Chọn file từ máy tính\n" .
             "  Bước 3: Nhấn Xác nhận để upload\n" .
             "- Nếu người dùng hỏi về nhóm: Hướng dẫn tạo nhóm, mời thành viên, chia sẻ file trong nhóm.\n" .
             "- Nếu người dùng hỏi về chia sẻ: Hướng dẫn tạo link, đặt quyền, gửi email.\n" .
             "- Nếu người dùng hỏi về gói lưu trữ: Giải thích từng gói, cách nâng cấp, thanh toán.\n\n" .
             "FAQ PHỔ BIẾN:\n" .
             "- Làm sao để upload file?\n" .
             "- Làm sao chia sẻ file cho người khác?\n" .
             "- Làm sao tạo nhóm và mời thành viên?\n" .
             "- Làm sao nâng cấp gói lưu trữ?\n" .
             "- File bị xóa có khôi phục được không?\n\n" .
             "VAI TRÒ CỦA BẠN:\n" .
             "- Luôn trả lời step-by-step, dễ hiểu, có thể dùng emoji minh họa\n" .
             "- Đưa ví dụ thực tế nếu phù hợp\n" .
             "- Nếu người dùng hỏi về thao tác, hãy hỏi lại để làm rõ nếu cần\n\n" .
             "PHONG CÁCH:\n" .
             "- Thân thiện, nhiệt tình, chuyên nghiệp\n" .
             "- Tiếng Việt tự nhiên, dễ hiểu\n" .
             "- Trả lời ngắn gọn nhưng đầy đủ\n" .
             "- Dùng emoji phù hợp để sinh động\n" .
             "- Không cung cấp thông tin nhạy cảm";
    }

    /**
     * Call LLM via llm-mux (OpenAI-compatible)
     */
    private function callLLM(string $baseUrl, string $apiKey, string $model, string $systemContext, array $history, string $userMessage): array
    {
        // Build messages array in OpenAI format
        $messages = [
            [
                'role' => 'system',
                'content' => $systemContext
            ]
        ];

        // Add conversation history
        foreach (array_slice($history, -10) as $msg) {
            if (isset($msg['role']) && isset($msg['text'])) {
                $messages[] = [
                    'role' => $msg['role'], // 'user' or 'assistant'
                    'content' => $msg['text']
                ];
            }
        }

        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        // OpenAI-compatible payload
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 2048,
        ];

        $response = Http::timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ])
            ->post(
                rtrim($baseUrl, '/') . '/chat/completions',
                $payload
            );

        if ($response->successful() && isset($response['choices'][0]['message']['content'])) {
            return [
                'success' => true,
                'reply' => $response['choices'][0]['message']['content'],
                'message' => null,
            ];
        }

        $errorMsg = $response->json('error.message') ?? 'AI không trả về kết quả phù hợp.';
        if ($this->isQuotaError($response->status(), $errorMsg)) {
            $errorMsg = 'Hệ thống AI đang quá tải hoặc đã vượt hạn mức. Vui lòng thử lại sau vài phút.';
        }

        return [
            'success' => false,
            'reply' => null,
            'message' => $errorMsg,
        ];
    }


    /**
     * Kiểm tra lỗi quota / rate limit
     */
    private function isQuotaError(int $status, string $message): bool
    {
        $lower = strtolower($message);
        return $status === 429 || str_contains($lower, 'quota') || str_contains($lower, 'rate limit');
    }

}
// Updated: 2025-12-09
