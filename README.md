Các bước để khởi chạy

Bước 1: Clone dự án về máy
git clone <đường-dẫn-kho-chứa-github-của-bạn>
cd <tên-thư-mục-dự-án>

Bước 2: Cài đặt các thư viện PHP
composer install

Bước 3: Tạo file cấu hình
copy .env.example qua .env

Bước 4: Cấu hình lại file .env cá nhân
php artisan key:generate

Bước 5: Cấu hình lại file .env cá nhân
GEMINI_API_KEY=XXXXXX

Bước 6: Chạy Migration (Tạo bảng cơ sở dữ liệu)
php artisan migrate

Bước 7:Khởi động Server
php artisan serve
