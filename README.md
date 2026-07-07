Các bước để khởi chạy

Bước 1: Clone dự án về máy
<br/>
git clone <đường-dẫn-kho-chứa-github-của-bạn>
cd <tên-thư-mục-dự-án>

Bước 2: Cài đặt các thư viện PHP
<br/>
composer install

Bước 3: Tạo file cấu hình
<br/>
copy .env.example qua .env

Bước 4: Cấu hình lại file .env cá nhân
<br/>
php artisan key:generate

Bước 5: Cấu hình lại file .env cá nhân
<br/>
GEMINI_API_KEY=XXXXXX

Bước 6: Chạy Migration (Tạo bảng cơ sở dữ liệu)
<br/>
php artisan migrate

Bước 7:Khởi động Server
<br/>
php artisan serve
