<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\SearchingNotification;

class ChatbotController extends Controller
{
    public function handleChat(Request $request)
    {
        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        $prompt = "Phân tích câu sau của người dùng: \"{$userMessage}\". "
            . "Nếu người dùng có ý định muốn gửi mail, thông báo việc gửi mail, hoặc ra lệnh gửi mail liên quan đến việc Long tìm tài liệu, hãy chỉ trả về đúng từ khóa: SEND_MAIL. "
            . "Nếu là câu trò chuyện bình thường khác, hãy trả về: NORMAL_CHAT.";

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'reply' => 'Lỗi kết nối API Gemini: ' . $response->body()
                ], 500);
            }

            $resultText = trim($response->json('candidates.0.content.parts.0.text'));

            if (str_contains($resultText, 'SEND_MAIL')) {

                Mail::to('vupvpk03660@gmail.com')->send(new SearchingNotification());

                return response()->json([
                    'status' => 'success',
                    'reply' => '🤖 Tôi đã hiểu ý bạn! Tôi đã kích hoạt hệ thống SMTP gửi mail cảnh báo "Long đang kiếm tài liệu" đến hộp thư thành công!'
                ]);
            }

            $normalResponse = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, [
                'contents' => [
                    ['parts' => [['text' => $userMessage]]]
                ]
            ]);

            return response()->json([
                'status' => 'normal',
                'reply' => $normalResponse->json('candidates.0.content.parts.0.text')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'reply' => 'Hệ thống có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
