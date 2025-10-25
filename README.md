E-Commerce API

RESTful API backend for an e-commerce platform built with Laravel 10.

Features
- User authentication with role-based access (Admin, Seller, Customer)
- Product and category management
- Order processing with status tracking
- Inventory management with transaction history
- Stock validation and automatic adjustments

 Requirements
- PHP 8.1+
- Composer
- MySQL

Installation

Clone and install dependencies:

git clone <repository-url>
ecommerce-platform
composer install

Set up environment:

cp .env.example .env

Configure your database in `.env`:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_task
DB_USERNAME=root
DB_PASSWORD=


Generate key and migrate:

php artisan key:generate
php artisan migrate
php artisan db:seed

Start server:


php artisan serve


Test Accounts

After seeding, you can use these accounts:
- Admin: admin@example.com / password
- Seller: seller@example.com / password
- Customer: customer@example.com / password


Architecture

The project uses a service layer pattern to separate business logic from controllers. 

Main services:

- AuthService: Handles registration, login, and token management
- OrderService: Manages order creation, status updates, and cancellations
- WarehouseService: Tracks inventory changes and maintains transaction history

Key implementation details:

Stock Management: Every stock change creates a warehouse transaction record with before/after values. Orders automatically deduct stock, and cancellations restore it. The system prevents orders when stock is insufficient.

Order Flow: Orders start as "pending" and can move through processing → shipped → completed. Cancellation is only allowed in pending or processing states and triggers automatic stock restoration.

Authorization: Three-tier approach - middleware for routes, form requests for validation, and additional checks in controllers for resource ownership.

Database Transactions: Critical operations like order placement use DB transactions to ensure data consistency.

Database Schema
Main tables:
- users - with role field (admin/seller/customer)
- categories - product categories
- products - includes stock field and seller reference
- orders - with status and total amount
- order_items - line items with price snapshot
- warehouse_transactions- audit log of all stock changes

Products belong to categories and sellers. Orders belong to customers and have many items. All stock movements are logged in warehouse_transactions.

Notes
- All API responses follow a consistent format with success,message, and data fields
- Soft deletes are used for products and categories
- Stock validation happens before order creation to prevent overselling
- Product prices are stored in order items to maintain historical accuracy

