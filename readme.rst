# DL Ideas — CodeIgniter E-Commerce App

A simple e-commerce web application built with **CodeIgniter 3**, **MySQL**, and **Stripe** payments, running fully inside **Docker**.

Users can browse products, place orders via Stripe Checkout, and view their order history with invoices and receipts. Admins can manage users, products, and all orders from a dashboard.

---

## Tech Stack

| Layer     | Technology             |
|-----------|------------------------|
| Backend   | PHP 7.3, CodeIgniter 3 |
| Database  | MySQL 8                |
| Payments  | Stripe Hosted Checkout |
| Email     | Gmail SMTP             |
| Container | Docker, Docker Compose |

---

## Prerequisites

Install [Docker](https://www.docker.com/products/docker-desktop) and verify:

```
docker -v
docker compose version
```

---

## Getting Started

**1. Start the containers**

```
docker compose up -d --build
```

**2. Run database migrations and seed default data**

```
curl http://localhost:8080/migrate
```

Expected output: `Migration + Seeding completed!`

This creates all tables and seeds two default accounts (see below).

**3. Open the app**

```
http://localhost:8080
```

---

## Default Accounts

| Role  | Email               | Password |
|-------|---------------------|----------|
| Admin | admin@example.com   | admin123 |
| User  | user@example.com    | user123  |

---

## Features

### Customer (User)

- Browse the product catalog on the home page
- Click **Buy Now** on any product to open the checkout form
- Fill in name, email, phone, shipping address, and quantity
- Pay securely via **Stripe Hosted Checkout** (redirects to Stripe's payment page)
- Get redirected back to a success page after payment
- Receive an **order confirmation email** after a successful payment
- View all past orders at `/my-orders`
- View a full **invoice** for any order (itemised, with shipping details)
- View a **receipt** for any order (transaction summary only — payment intent ID, charge ID, total paid)

### Admin

- Log in at `/admin/login`
- View the dashboard at `/admin/dashboard`
- Manage users: list at `/admin/users`, create at `/admin/users/create`
- Manage products: list at `/admin/products`, create at `/admin/products/create`
- View all customer orders at `/admin/orders`
- View the **invoice** or **receipt** for any order

---

## Stripe Configuration

Update `app/application/config/stripe.php` with your Stripe keys:

```php
$config['stripe_secret_key'] = 'sk_test_...';
$config['stripe_currency']   = 'usd';
```

Get your test keys from [dashboard.stripe.com/test/apikeys](https://dashboard.stripe.com/test/apikeys).

**Test card for Stripe Checkout:**

```
Card number : 378282246310005
Expiry      : 08/28
CVC         : 123
```

---

## Email Configuration (Gmail SMTP)

Order confirmation emails are sent via Gmail SMTP. Set these environment variables in `docker-compose.yml` under the `app` service:

```yaml
environment:
  SMTP_USER: your_gmail@gmail.com
  SMTP_PASS: your_gmail_app_password
```

> Use a [Gmail App Password](https://myaccount.google.com/apppasswords), not your normal Gmail password.
> If these variables are not set, emails are skipped silently and a note is written to the app logs.

---

## Database

The database connection is pre-configured for the Docker environment in `app/application/config/database.php`:

```php
$db['default']['hostname'] = 'db';
$db['default']['username'] = 'ci_user';
$db['default']['password'] = 'ci_pass';
$db['default']['database'] = 'ci_db';
```

To open a MySQL shell directly:

```
docker exec -it ci_db mysql -u ci_user -p
```

> Re-running `/migrate` will reset and re-seed the `users` and `products` tables. All other tables (orders, addresses, etc.) are preserved.

---

## Routes Reference

| URL                             | Who   | What                              |
|---------------------------------|-------|-----------------------------------|
| `/`                             | User  | Product catalog (home page)       |
| `/login`                        | Both  | Login page                        |
| `/logout`                       | Both  | Log out                           |
| `/buy-now/{slug}`               | User  | Checkout form for a product       |
| `/checkout/success`             | User  | Post-payment success page         |
| `/my-orders`                    | User  | My order history                  |
| `/my-orders/invoice/{id}`       | User  | Invoice for an order              |
| `/my-orders/receipt/{id}`       | User  | Receipt for an order              |
| `/admin/dashboard`              | Admin | Admin overview                    |
| `/admin/users`                  | Admin | User list                         |
| `/admin/products`               | Admin | Product list                      |
| `/admin/orders`                 | Admin | All orders                        |
| `/admin/orders/invoice/{id}`    | Admin | Invoice for any order             |
| `/admin/orders/receipt/{id}`    | Admin | Receipt for any order             |
| `/migrate`                      | Dev   | Run migrations and seed data      |
