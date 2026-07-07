<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SearchingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct() {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚨 Cảnh báo: Phát hiện hành vi tìm kiếm tài liệu!',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '<h3>Hệ thống Chatbot Thông Báo</h3><p>Phát hiện tài khoản tên <b>Long</b> đang thực hiện hành vi tìm kiếm tài liệu liên quan đến bạn trên hệ thống.</p>'
        );
    }
}