## About Laravel File Upload API

The file upload and sharing API is built with Laravel 12.10.0

## Installation

- **Clone the repository**
git clone https://github.com/NobulPlus/file-upload-api.git
cd file-upload-api
- **Install dependencies:**
composer install
- **Copy .env.example to .env and configure:**
cp .env.example .env
- **Generate Key:**
php artisan key:generate
- **Run migrations:**
php artisan migrate
- **Link storage**
php artisan storage:link
- **Start server:**
php artisan serve

## API Endpoints
- POST /api/upload: Upload files (multipart/form-data).
- GET /api/download/{token}: Download files.
- GET /api/uploads/stats/{token}: View stats.
- POST /api/register: Register user.
- POST /api/login: Login user.

## Testing
- Import file-upload-api.postman_collection.json.
- Test cleanup: php artisan clean:expired-uploads.

## Environment
See .env.example.
