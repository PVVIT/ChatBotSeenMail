<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class ChatbotController extends Controller
{
    public function handleChat(Request $request)
    {
        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $prompt = "Bạn là một trợ lý ảo phân tích dữ liệu chuyên nghiệp. Hãy đọc hiểu câu lệnh sau của người dùng: \"{$userMessage}\".\n\n"
            . "Nhiệm vụ của bạn:\n"
            . "1. Xác định xem người dùng có thực sự muốn gửi email/mail hay không.\n"
            . "2. Nếu CÓ ý định gửi mail, trích xuất: địa chỉ email nhận, tiêu đề (title) và nội dung (content) từ câu của họ.\n\n"
            . "Quy định trả về:\n"
            . "- Nếu CÓ ý định gửi mail, trả về CHÍNH XÁC một chuỗi JSON có định dạng sau (không kèm lời giải thích, không bọc trong ký tự markdown):\n"
            . "{\"intent\": \"SEND_MAIL\", \"email\": \"email_trich_xuat_duoc\", \"title\": \"tieu_de_trich_xuat_duoc\", \"content\": \"noi_dung_trich_xuat_duoc\"}\n"
            . "- Nếu là câu trò chuyện, hỏi đáp bình thường không phải lệnh gửi mail, trả về CHÍNH XÁC chuỗi JSON sau:\n"
            . "{\"intent\": \"NORMAL_CHAT\"}\n\n"
            . "Chú ý: Nếu trong câu lệnh gửi mail không có title, hãy mặc định title là 'Thông báo từ Chatbot'. Nếu không có email, mặc định để trống.";

        try {
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
            $resultText = trim(str_replace(['```json', '```'], '', $resultText));
            
            $data = json_decode($resultText, true);

            if (isset($data['intent']) && $data['intent'] === 'SEND_MAIL') {
                $toEmail = !empty($data['email']) ? $data['email'] : 'vupvpk03660@gmail.com'; 
                $subject = !empty($data['title']) ? $data['title'] : 'Thông báo từ Chatbot';
                $mailContent = !empty($data['content']) ? $data['content'] : 'Không có nội dung được đính kèm.';

                Mail::raw($mailContent, function ($message) use ($toEmail, $subject) {
                    $message->to($toEmail)
                            ->subject($subject);
                });

                return response()->json([
                    'status' => 'success',
                    'reply' => "✉️ Tôi đã gửi thành công email tới địa chỉ **{$toEmail}** với tiêu đề **'{$subject}'** và nội dung: \"_{$mailContent}_\"."
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