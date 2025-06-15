# Lodge Manager - Backend

Phần backend của ứng dụng Lodge Manager, được xây dựng trên nền tảng Laravel. Cung cấp các API để quản lý tòa nhà, phòng, hợp đồng, và các dịch vụ liên quan.

## Yêu Cầu Hệ Thống

- PHP 8.1 trở lên
- Composer 2.0 trở lên
- SQLite (đã tích hợp sẵn)

## Hướng Dẫn Cài Đặt

### 1. Cài đặt môi trường

```bash
# Clone dự án (nếu chưa có)
git clone https://github.com/phanduythinh/LodgeManager-Web.git

# Di chuyển vào thư mục backend
cd LodgeManager-Web/be

# Cài đặt các thư viện PHP cần thiết
composer install
```

### 2. Cấu hình môi trường

```bash
# Sao chép file cấu hình
cp .env.example .env

# Tạo key ứng dụng
php artisan key:generate

# Tạo file database SQLite
type nul > database\database.sqlite  # Windows
# hoặc
# touch database/database.sqlite    # macOS/Linux
```

### 3. Cấu hình cơ sở dữ liệu

Mở file `.env` và cập nhật các thông số kết nối:

```
DB_CONNECTION=sqlite
DB_DATABASE=/đường/dẫn/đến/LodgeManager-Web/be/database/database.sqlite
```

### 4. Chạy migrations và seed dữ liệu mẫu

```bash
php artisan migrate --seed
```

### 5. Khởi động server

```bash
php artisan serve
```

Server sẽ chạy tại: http://127.0.0.1:8000

## API Endpoints

- `GET /api/toa-nha`: Lấy danh sách tòa nhà
- `GET /api/phong`: Lấy danh sách phòng
- `GET /api/khach-hang`: Lấy danh sách khách hàng
- `GET /api/hop-dong`: Lấy danh sách hợp đồng

## Xử lý lỗi thường gặp

### 1. Lỗi cơ sở dữ liệu
- **Triệu chứng:** Lỗi khi chạy migrations hoặc truy vấn
- **Cách khắc phục:**
  ```bash
  # Xóa file cũ (nếu có)
  rm database/database.sqlite
  
  # Tạo file mới
  type nul > database\database.sqlite  # Windows
  # hoặc
  # touch database/database.sqlite    # macOS/Linux
  
  # Chạy lại migrations
  php artisan migrate --seed
  ```

### 2. Lỗi CORS
- **Triệu chứng:** Không thể gọi API từ frontend
- **Cách khắc phục:**
  - Kiểm tra file `config/cors.php`
  - Đảm bảo đã thêm domain của frontend vào `allowed_origins`
  - Mặc định đã hỗ trợ `http://localhost:5173`

### 3. Lỗi thiếu thư viện
- **Triệu chứng:** Lỗi khi chạy lệnh `composer install`
- **Cách khắc phục:**
  ```bash
  # Xóa thư mục vendor và file composer.lock
  rm -r vendor
  rm composer.lock
  
  # Cài đặt lại
  composer install
  ```

## Chạy kiểm thử

```bash
php artisan test
```

## Kết nối với Frontend

Để kết nối với frontend React:
1. Đảm bảo frontend đang chạy trên port 5173
2. Backend mặc định đã hỗ trợ CORS cho `http://localhost:5173`
3. Trong file cấu hình frontend, đặt `VITE_API_BASE_URL=http://localhost:8000/api`
