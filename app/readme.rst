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

## Stripe configuration
--------------------

Update the Stripe config file:

* ``application/config/stripe.php``

Required values:

* ``stripe_secret_key``: your Stripe secret key (for example, ``sk_test_...``)
* ``stripe_currency``: currency code used for payments (default: ``usd``)

for my stripe keys please check mail 
---------------------------------------

## Email Configuration (Gmail SMTP)

Order confirmation emails are sent via Gmail SMTP. Set these environment variables in `docker-compose.yml` under the `app` service:

config/config.php

```
  SMTP_USER: your_gmail@gmail.com
  SMTP_PASS: your_gmail_app_password
```

> Use a [Gmail App Password](https://myaccount.google.com/apppasswords), not your normal Gmail password.
> If these variables are not set, emails are skipped silently.

---

Run migrations and seeders
--------------------------

This project includes a migration controller at ``/migrate``.
Opening that route will:

* run all migrations up to the latest version
* truncate the ``users`` and ``products`` tables
* seed default users and products

Run it in the browser:

.. code-block:: text

	http://localhost:8080/migrate

Or trigger it from the command line:

.. code-block:: bash

	curl http://localhost:8080/migrate

Expected result:

.. code-block:: text

	Migration + Seeding completed!

Seeded data
-----------

The migration route currently runs these seeders:

* ``UserSeeder``
* ``ProductSeeder``

The default seeded users are:

* admin@example.com / admin123
* user@example.com / user123

Notes
-----

* The table truncation in the migration controller is intended for development use.
* Re-running the migration route will clear and re-seed the ``users`` and ``products`` tables.

Stripe hosted checkout integration
---------------------------------

This project uses Stripe Hosted Checkout (redirect flow), not a custom card form.

Checkout behavior
-----------------

* Only customer users can place orders.
* Admin sessions are blocked from checkout.
* Checkout starts from ``/buy-now/{product-slug}`` and posts to ``/checkout/place-order/{product-slug}``.
* The backend creates a Stripe Checkout Session and redirects to the Stripe-hosted payment page.


What data is collected in checkout form
---------------------------------------

The checkout form collects:

* first name, last name, email, phone
* shipping address (line 1, optional line 2, city, state, postal code, country ISO-2)
* quantity

The backend validates required fields and email format before creating the Stripe session.

------------------------
**Test card for Stripe Checkout:**

DUMMY CREDIT CARD
NUMBER: 378282246310005
EXPIRY: 08/28
CVC: 123


Test flow
---------

1. Log in with a user account (for seeded data: ``user@example.com / user123``).
2. Open the product list and click Buy Now.
3. Fill checkout form and submit.
4. Confirm redirect to ``checkout.stripe.com``.
5. Complete payment with Stripe test cards.
6. Verify redirect to success/cancel page.
7. Check for order confirmation email (if email is configured).
8. View order in ``/my-orders`` and check invoice/receipt.

REST API
--------

The application exposes a stateless REST API for mobile or third-party
consumers. Authentication uses opaque bearer tokens stored in the
``api_tokens`` table (created by migration ``008``).

**Authentication**

.. code-block:: text

    POST /api/login

Request body (JSON):

.. code-block:: json

    {
        "email": "user@example.com",
        "password": "user123"
    }

Response (``200 OK``):

.. code-block:: json

    {
        "status": "success",
        "token": "a1b2c3...64-char-hex-string",
        "expires_in": 86400,
        "user": {
            "id": 2,
            "name": "Test User",
            "email": "user@example.com",
            "role": "customer"
        }
    }

The token is valid for **24 hours**. Include it in subsequent requests via
the ``Authorization`` header.

**Get invoice**

.. code-block:: text

    GET /api/invoices/:order_id
    Authorization: Bearer <token>

Returns order details, line items, and shipping address as JSON.
Regular users can only access their own orders; admins can access any order.

Response (``200 OK``):

.. code-block:: json

    {
        "status": "success",
        "invoice": {
            "order_id": 1,
            "stripe_session_id": "cs_test_...",
            "payment_status": "paid",
            "total": "49.99",
            "created_at": "2026-04-01 12:00:00",
            "customer": { "name": "Test User", "email": "user@example.com" },
            "shipping_address": { "line1": "123 Main St", "city": "Springfield", "..." : "..." },
            "items": [
                { "product_name": "Widget", "quantity": 1, "unit_price": "49.99", "line_total": "49.99" }
            ]
        }
    }

**Get receipt**

.. code-block:: text

    GET /api/receipts/:order_id
    Authorization: Bearer <token>

Returns the same data as the invoice endpoint plus a ``receipt`` wrapper
with payment confirmation fields (``payment_intent``, ``charge_id``).

**API routes**

* ``api/login`` → ``Api::login``
* ``api/invoices/(:num)`` → ``Api::invoice/$1``
* ``api/receipts/(:num)`` → ``Api::receipt/$1``

**CSRF exemption**

API routes are excluded from CSRF protection in
``application/config/config.php`` since they use token-based
authentication instead of browser cookies.

**Token storage**

Tokens are stored in the ``api_tokens`` table:

.. code-block:: text

    id          INT AUTO_INCREMENT PRIMARY KEY
    user_id     INT NOT NULL (FK → users.id)
    token       VARCHAR(64) UNIQUE NOT NULL
    expires_at  DATETIME NOT NULL
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP

Run migration ``008`` to create this table:

.. code-block:: bash

    curl http://localhost:8080/migrate

**Error responses**

All error responses follow the same format:

.. code-block:: json

    {
        "status": "error",
        "message": "Invalid or expired token"
    }

Common HTTP status codes:

* ``400`` — missing or invalid request body
* ``401`` — missing, invalid, or expired token
* ``403`` — access denied (user trying to view another user's order)
* ``404`` — order not found
* ``405`` — wrong HTTP method


ER Diagram
----------

The database contains six tables. Relationships are shown below.

ER diagram: 
https://asset.cloudinary.com/dndfmewv4/a6bbe80f6a428eb88d8a8b7e069cc5c0

Relationships:

* ``users`` 1 --- N ``addresses`` (user_id)
* ``users`` 1 --- N ``orders`` (user_id)
* ``addresses`` 1 --- N ``orders`` (address_id)
* ``orders`` 1 --- N ``order_items`` (order_id)
* ``products`` 1 --- N ``order_items`` (product_id)
* ``users`` 1 --- N ``logs`` (user_id, nullable)


Restricting actions to within the web application
--------------------------------------------------

To ensure system users can only perform sensitive actions
within the web application and not via direct URL manipulation or external
requests, the following measures are applied or recommended:

1. **Session-based authentication**
   Every controller method that renders a protected page checks for a valid
   session (``admin_logged_in`` or ``user_logged_in``). If the session is
   missing the user is redirected to the login page.

2. **Role-based access control**
   Admin and user sessions are separated. Admin controllers verify
   ``admin_logged_in`` before serving admin pages; user controllers verify
   ``user_logged_in``. A user cannot access an admin route and vice versa.

3. **Ownership validation**
   When a user requests an invoice or receipt, the backend verifies that
   the order belongs to the logged-in user (``user_id`` matches session).
   This prevents horizontal privilege escalation where one user tries to
   view another user's invoice by changing the order ID in the URL.

4. **CSRF protection**
   Enabled in ``application/config/config.php``. All state-changing POST
   requests require a valid CSRF token. This prevents an external site from
   forging requests on behalf of a logged-in user.

5. **Further enhancements**

   * **Signed/expiring URLs** — Generate time-limited, HMAC-signed tokens
     for invoice download links so they cannot be bookmarked or shared.
   * **Content-Disposition headers** — Serve invoice PDFs with
     ``Content-Disposition: attachment`` and proper ``Content-Type`` so
     browsers download rather than render, reducing screenshot/link-sharing
     risk.
   * **Audit logging** — The ``logs`` table already records user actions
     with IP addresses. Extend it to log every invoice view for compliance.
   * **Rate limiting** — Throttle invoice/receipt endpoints to prevent
     enumeration attacks.


API considerations for mobile applications
-------------------------------------------

If a REST API were to be built on top of this CodeIgniter 2 project for
mobile consumption, the following factors must be addressed:

**Authentication and authorisation**

* Replace session-based auth with stateless **JWT (JSON Web Token)** or
  **OAuth 2.0** bearer tokens. Mobile apps cannot rely on browser cookies.
* Issue short-lived access tokens with longer-lived refresh tokens.
* Scope tokens by role (admin vs user) to enforce the same access rules.

**API design**

* Follow RESTful conventions: ``GET /api/products``, ``POST /api/orders``,
  ``GET /api/orders/{id}/invoice``.
* Version the API (``/api/v1/...``) so mobile releases are not broken by
  backend changes.
* Return JSON responses with consistent envelope
  (``{"status": "success", "data": {...}}``).

**Validation and security**

* Validate and sanitise all input server-side. Mobile clients can be
  tampered with.
* Enforce HTTPS for all API endpoints.
* Implement rate limiting per API key / token to prevent abuse.
* Use CORS headers only if a web SPA also consumes the same API.

**Limitations of CodeIgniter 2**

* CI2 has no built-in REST controller. A third-party library such as
  ``chriskacerguis/codeigniter-restserver`` would be needed, or upgrade to
  CodeIgniter 4 which has native API response traits.
* CI2 lacks native support for content negotiation, response transformers,
  and middleware pipelines. Each controller must manually output JSON.
* No built-in support for API versioning, token authentication, or
  request throttling — all must be implemented or added via libraries.
* PHP 7.3 (as used in the Dockerfile) is end-of-life. A mobile API should
  run on PHP 8.1+ for security patches and performance.

**Other considerations**

* Push notifications (Firebase Cloud Messaging) for order status updates.
* Pagination (``?page=1&per_page=20``) for product and order listings.
* Image optimisation — serve product images in WebP and multiple sizes for
  mobile bandwidth.
* Offline support — design the API so mobile apps can cache product
  catalogues and sync orders when connectivity returns.


Deployment strategy — staging vs production
-------------------------------------------

**Staging environment**

* Mirror the production stack (PHP, MySQL, Apache) using the same Docker
  Compose setup on a single cloud VM (e.g. AWS EC2 ``t3.small``,
  ~USD 15/month).
* Point a subdomain (``staging.example.com``) to this VM.
* Use a separate MySQL database (``ci_db_staging``) so test data never
  leaks into production.
* Deploy via CI/CD pipeline (GitHub Actions):
  ``push to staging branch → build Docker image → docker compose up``.
* Stripe test keys are used in staging; production keys are never present.

**Production environment**

.. code-block:: text

    Internet
       │
    [ CloudFlare / AWS CloudFront CDN ]
       │
    [ Application Load Balancer ]
       │
    ┌───┴───┐
    │ ECS / │  (or EC2 Auto Scaling Group)
    │ Fargate│  running ci_app container
    └───┬───┘
        │
    [ Amazon RDS MySQL 8 ]  (Multi-AZ for failover)
        │
    [ S3 bucket ]  (product images + invoice PDFs)

* **Compute** — AWS ECS Fargate or EC2 behind an ALB. Auto-scales on CPU.
  Estimated cost: 1–2 tasks at ~USD 30–50/month.
* **Database** — Amazon RDS MySQL ``db.t3.micro`` Multi-AZ.
  Estimated cost: ~USD 25–30/month.
* **Storage** — S3 for uploads (product images). Negligible cost at low
  volume.
* **CDN** — CloudFront or CloudFlare in front of the ALB for static assets
  and DDoS protection. Free tier or ~USD 5/month.
* **SSL** — AWS Certificate Manager (free) or Let's Encrypt.
* **Total estimated cost** — USD 70–100/month for a small-scale deployment.

**Deployment strategy**

* **Blue-green deployment** — Run two ECS task sets behind the ALB. Deploy
  the new version to the green set, run smoke tests, then switch traffic.
  Instant rollback by switching back to blue.
* **Database migrations** — Run ``/migrate`` as a one-off ECS task before
  switching traffic, not via a public URL.
* **Environment variables** — Store secrets (DB password, Stripe keys) in
  AWS Secrets Manager or SSM Parameter Store, not in config files.
* **Monitoring** — CloudWatch for logs and alarms; Sentry or Rollbar for
  PHP error tracking.


Additional tools and libraries
------------------------------

**Currently used**

* **CodeIgniter 2.x** — PHP MVC framework providing routing, controllers,
  models, views, form validation, database abstraction, and session
  management.
* **Bootstrap 5.3** — Front-end CSS framework used for responsive layout
  and UI components across all views.
* **Stripe PHP (via Hosted Checkout)** — Payment processing using
  server-side cURL calls to the Stripe Checkout Sessions API.
* **Docker + Docker Compose** — Containerised development environment with
  PHP 7.3-Apache and MySQL 8.0 services.
* **PHPUnit** — Unit testing framework (configured under ``tests/``).
* **vlucas/phpdotenv** — Environment variable management via ``.env`` files
  (present in Composer dependencies).

**Recommended additions**

* **CodeIgniter REST Server** (``chriskacerguis/codeigniter-restserver``) —
  If a mobile API is needed, this library adds REST controller support to
  CI2.
* **Redis / Memcached** — For session storage in multi-server production
  deployments (memcached config already exists at
  ``application/config/memcached.php``).
* **Monolog** — Structured logging with support for external sinks
  (CloudWatch, Papertrail) instead of flat file logs.
* **PHP-CS-Fixer / PHPStan** — Code style enforcement and static analysis
  to catch bugs before deployment.
* **GitHub Actions** — CI/CD pipeline to run tests, build Docker image, and
  deploy to staging/production automatically.
* **Sentry** — Real-time error tracking and alerting for production.
* **Let's Encrypt / Certbot** — Free automated SSL certificate management.
