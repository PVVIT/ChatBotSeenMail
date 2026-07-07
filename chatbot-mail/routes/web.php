<?php

use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

// 1. Đường dẫn GET để hiển thị giao diện (Địa chỉ bạn sẽ truy cập trên trình duyệt)
Route::get('/chat', function () {
    return view('chat');
});

// 2. Đường dẫn POST để nhận tin nhắn từ ô chat truyền lên (Không truy cập trực tiếp dòng này)
Route::post('/api/chatbot/chat', [ChatbotController::class, 'handleChat']);