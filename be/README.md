# Lodge Manager Backend

Backend API cho ứng dụng Lodge Manager được xây dựng bằng Laravel.

## Yêu cầu

-   PHP >= 8.1
-   Composer
-   MySQL >= 5.7
-   Node.js & NPM

## Cài đặt

1. Clone repository:

```bash
git clone <repository-url>
cd be
```

2. Cài đặt dependencies:

```bash
composer install
```

3. Tạo file .env:

```bash
cp .env.example .env
```

4. Tạo application key:

```bash
php artisan key:generate
```

5. Cấu hình database trong file .env:

```bash
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=lodge_manager
# DB_USERNAME=root
# DB_PASSWORD=
```

6. Chạy migrations:

```bash
php artisan migrate
```

7. Chạy seeders (nếu cần):

```bash
php artisan db:seed
```

8. Khởi động server:

```bash
php artisan serve
```

## API Endpoints

### Resources

-   GET /api/buildings - Lấy danh sách tòa nhà
-   POST /api/buildings - Tạo tòa nhà mới
-   GET /api/buildings/{id} - Lấy thông tin tòa nhà
-   PUT /api/buildings/{id} - Cập nhật tòa nhà
-   DELETE /api/buildings/{id} - Xóa tòa nhà

(Tương tự cho các resource khác: rooms, contracts, customers, invoices, services)

## Kết nối với Frontend

1. Cấu hình CORS trong `config/cors.php`:

```php
'allowed_origins' => ['http://localhost:3000'], // URL của frontend React
```

2. Cấu hình Sanctum trong `config/sanctum.php`:

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,localhost:3000')),
```

3. Trong frontend React, sử dụng axios để gọi API:

```javascript
import axios from "axios";

const api = axios.create({
    baseURL: "http://localhost:8000/api",
    withCredentials: true,
});

// Thêm token vào header
api.interceptors.request.use((config) => {
    const token = localStorage.getItem("token");
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});
```

## Testing

```bash
php artisan test
```
