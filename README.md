# Do-an-ly-thuyet

## Cài đặt shopping/authoriztion
- tạo database shopping/authorization database trên mysql(có người dùng root/root)
- cd shopping/authoriztion, composer install (có thể cần cp .env.example .env )
- run php artisan migration trên teminal
- run php artisan serve trên terminal để chạy server
Lưu ý: shopping hiện đang chạy trên port 8001, authoriztion hiện đang chạy trên port 8002

## Cài đặt gateway
- cd gateway -> npm install (port chạy 8010)