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

Stripe configuration
--------------------

Update the Stripe config file:

* ``application/config/stripe.php``

Required values:

* ``stripe_secret_key``: your Stripe secret key (for example, ``sk_test_...``)
* ``stripe_currency``: currency code used for payments (default: ``usd``)

Routes used by checkout
-----------------------

* ``buy-now/(:any)`` -> ``user/buy/$1``
* ``checkout/place-order/(:any)`` -> ``user/placeOrder/$1``
* ``checkout/success`` -> ``user/paymentSuccess``
* ``checkout/failure`` -> ``user/paymentFailure``

What data is collected in checkout form
---------------------------------------

The checkout form collects:

* first name, last name, email, phone
* shipping address (line 1, optional line 2, city, state, postal code, country ISO-2)
* quantity

The backend validates required fields and email format before creating the Stripe session.

Test flow
---------

1. Log in with a user account (for seeded data: ``user@example.com / user123``).
2. Open the product list and click Buy Now.
3. Fill checkout form and submit.
4. Confirm redirect to ``checkout.stripe.com``.
5. Complete payment with Stripe test cards.
6. Verify redirect to success/cancel page.

Troubleshooting
---------------

* If checkout does not redirect, check the purchase notice shown on checkout page.
* If Stripe reports invalid request parameters, confirm that required shipping fields are filled.
* In local non-production environments, the app retries once without SSL verification when CA certs are missing.
