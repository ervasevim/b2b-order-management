# B2B Order Management

A Laravel-based REST API project using Docker for containerization, Redis for caching, and Laravel Passport for API
authentication.

---

## 🚀 Requirements

- Docker & Docker Compose
- Git
- PHP >= 8.1 (if running outside Docker)
- Composer
- Redis CLI (optional)

---

## Base URL

All API endpoints and frontend routes use the following base URL: http://localhost:22000

## Getting Started

### 1. Clone the Repository and Setup Environment

Clone the repository and prepare the environment file:

```bash
git clone https://github.com/ervasevim/-b2b-order-management.git
cd -b2b-order-management.git
cp .env.example .env


php artisan passport:keys
php artisan passport:client --personal
```

### 2. Connect to Docker Container

To connect to the PHP container where you will run PHP commands:

```bash

docker compose up -d --build

docker exec -it  b2b-order-management-php-fpm bash
```

### 3. Run Database Migrations and Seed

Inside the container, run the following commands to create database tables and seed initial data:

```bash
composer install
php artisan migrate
php artisan db:seed
```

---

## API Routes

### Authentication

| Method | Endpoint  | Description                |
|--------|-----------|----------------------------|
| POST   | /register | Register a new user        |
| POST   | /login    | Login and get access token |

---

### Products

| Method | Endpoint       | Description                | Access     |
|--------|----------------|----------------------------|------------|
| GET    | /products      | Get all products           | Public     |
| POST   | /products      | Create a new product       | Admin only |
| PUT    | /products/{id} | Update an existing product | Admin only |
| DELETE | /products/{id} | Delete a product           | Admin only |

---

### Orders

| Method | Endpoint        | Description                          | Access                |
|--------|-----------------|--------------------------------------|-----------------------|
| GET    | /orders         | Get orders of the authenticated user | Authorized users only |
| POST   | /orders         | Place a new order                    | Authorized users only |
| GET    | /orders/{order} | Get details of a specific order      | Authenticated         |

---
