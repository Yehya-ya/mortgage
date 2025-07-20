# Mortgage Loan Calculator - Laravel Application

A Mortgage Loan Calculator build with laravel and react.

## Requirements

- PHP 8.4 or higher
- Composer
- Node.js 22+ and npm
- MySQL/PostgreSQL/SQLite database

## Run the application locally

### 1. Clone the Repository

```bash
git clone https://github.com/Yehya-ya/mortgage.git
cd mortgage
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. (optional) Set Database Configuration (if you don't want to use sqlite)

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mortgage_calculator
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Start Laravel Development Server

```bash
php artisan serve
```

### 8. Start Frontend Development Server 

```bash
npm run dev
```

### Run Tests

```bash
php artisan test
```

