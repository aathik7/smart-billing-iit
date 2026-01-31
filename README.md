# Smart Subscription & Usage Billing System

## Overview

This project is a backend implementation of a **Smart Subscription & Usage-Based Billing System**, developed as part of the **Second Round Technical Assignment**.

The system simulates how real SaaS platforms manage:
- user subscriptions
- monthly usage limits
- billing cycles
- usage enforcement

The focus of this project is **backend architecture, business logic correctness, and maintainability**, not UI or payment processing.

---

## Tech Stack

- PHP 8.1+
- Laravel 11
- MySQL (local / production)
- SQLite (in-memory) for testing
- JWT Authentication (tymon/jwt-auth)

---

## Features

### Authentication
- User registration and login
- JWT-based authentication
- Secure password hashing
- Protected APIs

### Subscription Plans

Plans are stored in the database and configurable.

| Plan | Price | Monthly Usage Limit |
|----|----|----|
| Free | ₹0 | 100 |
| Pro | ₹999 | 10,000 |
| Enterprise | ₹4,999 | Unlimited |

- Unlimited plans are never blocked
- Plans are fetched dynamically from the database

### User Subscription
- A user can have only **one active subscription**
- Each subscription includes:
  - start date
  - end date (monthly billing)
  - status (`active` / `expired`)
- Existing subscriptions are automatically expired when a new one is created

### Usage Tracking
- Each API call consumes **1 usage unit**
- Usage is tracked per user per billing cycle
- Usage resets automatically when a new billing cycle begins

### Usage Enforcement
- Users without an active subscription cannot consume usage
- Free and Pro plans are blocked when limits are exceeded
- Enterprise plan users are never blocked

---

## Architecture & Design Decisions

### Service-Based Design
- Controllers are kept thin
- All business logic and database interactions are handled inside **Service classes**
- Improves readability, maintainability, and testability

Controller → Service → Model


### API-Only Application
- No frontend or UI is implemented
- APIs are intended to be consumed via Postman or similar API clients

### No Payment Gateway
- Payment processing is intentionally skipped
- Subscription activation is assumed to be successful
- The design allows easy future integration of payment gateways

---

## API Endpoints

### Authentication (Public)

| Method | Endpoint | Description |
|----|----|----|
| POST | /api/auth/register | Register a new user |
| POST | /api/auth/login | Login and receive JWT |
| POST | /api/auth/logout | Logout and expires the token |

### Protected APIs (JWT Required)

| Method | Endpoint | Description |
|----|----|----|
| GET | /api/plans | List available plans |
| POST | /api/subscribe | Subscribe to a plan |
| GET | /api/subscription | Get current subscription |
| POST | /api/usage/consume | Consume one usage unit |
| GET | /api/usage/stats | Get usage statistics |

---

## Setup Instructions

### Prerequisites
- PHP 8.1+
- Composer
- MySQL

### Installation

```bash
git clone <repository-url>
cd smart-billing
composer install

## Environment Setup

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret

## Update database credentials in .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_billing
DB_USERNAME=root
DB_PASSWORD=secret

## Migrate & Seed

php artisan migrate --seed

## Run the Application

php artisan serve
The application will be available at:
http://127.0.0.1:8000

## API Usage Notes

Required Headers for Protected APIs
Authorization: Bearer <JWT_TOKEN>
Accept: application/json

Setting Accept: application/json ensures proper JSON responses for authentication and error handling in an API-only Laravel application.

## Error Handling

All errors return JSON responses

Proper HTTP status codes are used:

401 → Unauthenticated or invalid token
403 → Business rule violation (e.g., no active subscription)
429 → Usage limit exceeded
Stack traces are disabled in production (APP_DEBUG=false)

## Testing

Basic unit tests are included to validate core business logic.

What is Tested

Usage blocked without subscription
Subscription creation
Usage limit enforcement
Unlimited plan behavior

Run Tests
php artisan test

Tests run using an in-memory SQLite database and do not affect production data.