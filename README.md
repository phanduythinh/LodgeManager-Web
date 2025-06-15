# LodgeManager Web Application

A comprehensive web application for managing lodging facilities, built with React.js (Frontend) and Laravel (Backend).

## 🚀 Features

- **Building Management**: Manage multiple buildings and their details
- **Room Management**: Track room status, types, and assignments
- **Customer Management**: Maintain customer information and history
- **Contract Management**: Handle rental contracts and service agreements
- **Billing**: Generate and track invoices and payments

## 🛠️ Prerequisites

- **Backend**:
  - PHP 8.1 or higher
  - Composer 2.0 or higher
  - SQLite (included) or MySQL/PostgreSQL
  - Node.js 16.0+ (for frontend development)

- **Frontend**:
  - Node.js 16.0 or higher
  - npm 8.0 or higher

## 🚀 Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/phanduythinh/LodgeManager-Web.git
cd LodgeManager-Web
```

### 2. Backend Setup

```bash
# Navigate to backend directory
cd be

# Install PHP dependencies
composer install

# Copy environment file and generate application key
cp .env.example .env
php artisan key:generate

# Set up SQLite database
touch database/database.sqlite

# Configure .env for SQLite
# Edit .env and set:
# DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/LodgeManager-Web/be/database/database.sqlite

# Run migrations and seed the database
php artisan migrate --seed

# Start the backend server
php artisan serve
```

The backend will be available at `http://127.0.0.1:8000`

### 3. Frontend Setup

```bash
# Open a new terminal and navigate to frontend directory
cd ../fe

# Install dependencies
npm install

# Start the development server
npm run dev
```

The frontend will be available at `http://localhost:5173`

## 🔧 Environment Configuration

### Backend (`.env`)

Ensure these settings are properly configured in your `.env` file:

```
APP_NAME=LodgeManager
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/LodgeManager-Web/be/database/database.sqlite
```

### Frontend (`.env`)

Create a `.env` file in the `fe` directory with:

```
VITE_API_BASE_URL=http://localhost:8000/api
```

## 🧪 Running Tests

### Backend Tests

```bash
cd be
php artisan test
```

### Frontend Tests

```bash
cd fe
npm run test
```

## 🐳 Docker Support (Optional)

If you prefer using Docker, you can use the included `docker-compose.yml`:

```bash
docker-compose up -d
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

For support, please open an issue in the GitHub repository.

## 📚 Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [React Documentation](https://reactjs.org/)
- [Vite Documentation](https://vitejs.dev/guide/)

## 🔗 Useful Links

- [API Documentation](http://localhost:8000/api/documentation) (when running locally)
- [PHPMyAdmin](http://localhost:8080) (if using Docker)
