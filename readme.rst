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

**2. Open the app**

```
http://localhost:8080
```

**3. For project readme open**

app/readme.rst

```