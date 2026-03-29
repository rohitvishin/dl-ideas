<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo html_escape($title); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #eef3f1;
            --panel: #ffffff;
            --text: #13313f;
            --muted: #667f8a;
            --accent: #0c756e;
            --accent-strong: #0a5b56;
            --warm: #f2ba67;
            --border: rgba(19, 49, 63, 0.14);
            --shadow: 0 24px 50px rgba(13, 43, 56, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;
            font-family: 'Manrope', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(12, 117, 110, 0.2), transparent 34%),
                radial-gradient(circle at 90% 10%, rgba(242, 186, 103, 0.24), transparent 34%),
                linear-gradient(155deg, #f7fbf9 0%, var(--bg) 62%, #ebf2ef 100%);
            padding: 24px;
        }

        .shell {
            max-width: 1180px;
            margin: 0 auto;
            border: 1px solid rgba(255, 255, 255, 0.72);
            border-radius: 22px;
            overflow: hidden;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 20px;
            color: #ecf8f8;
            background: linear-gradient(140deg, #0d415c 0%, #0c756e 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .brand {
            text-decoration: none;
            color: inherit;
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.02em;
            color: #244352;
            background: linear-gradient(145deg, #f4c57b, #ffe7bf);
        }

        .brand-copy {
            display: grid;
            gap: 2px;
        }

        .brand-title {
            font-size: 16px;
            font-weight: 800;
            line-height: 1;
        }

        .brand-sub {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(236, 248, 248, 0.76);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 9px 13px;
            text-decoration: none;
            color: #f4fcfc;
            border: 1px solid rgba(255, 255, 255, 0.26);
            background: rgba(255, 255, 255, 0.1);
            font-size: 13px;
            font-weight: 700;
        }

        .wrap {
            padding: 24px;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 18px;
        }

        .panel {
            border: 1px solid var(--border);
            border-radius: 18px;
            background: #ffffff;
            padding: 18px;
        }

        .panel h1,
        .panel h2 {
            margin: 0;
            letter-spacing: -0.02em;
        }

        .panel h1 {
            font-size: clamp(28px, 3.2vw, 36px);
        }

        .sub {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.7;
            font-size: 14px;
        }

        .form-grid {
            margin-top: 18px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .field {
            display: grid;
            gap: 7px;
        }

        .field.full {
            grid-column: span 2;
        }

        .field label {
            font-size: 12px;
            color: #32525f;
            font-weight: 700;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .field input,
        .field textarea,
        .field select {
            width: 100%;
            border: 1px solid rgba(19, 49, 63, 0.2);
            border-radius: 10px;
            padding: 11px 12px;
            font-size: 14px;
            color: var(--text);
            background: #fbfdfd;
            outline: 0;
        }

        .field textarea {
            min-height: 94px;
            resize: vertical;
        }

        .checkout-actions {
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .notice {
            margin-top: 14px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            border: 1px solid rgba(173, 57, 57, 0.25);
            color: #8d2e2e;
            background: rgba(173, 57, 57, 0.09);
        }

        .login-gate {
            margin-top: 18px;
            border: 1px solid rgba(19, 49, 63, 0.16);
            border-radius: 14px;
            padding: 16px;
            background: linear-gradient(145deg, rgba(12, 117, 110, 0.08), rgba(242, 186, 103, 0.14));
        }

        .login-gate p {
            margin: 0 0 12px;
            color: #32515c;
            line-height: 1.7;
            font-size: 14px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 12px 16px;
            text-decoration: none;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 800;
            border: 0;
        }

        .btn-primary {
            color: #ffffff;
            background: linear-gradient(145deg, var(--accent), #16846e);
            box-shadow: 0 12px 20px rgba(11, 117, 110, 0.24);
        }

        .btn-secondary {
            color: var(--accent-strong);
            border: 1px solid rgba(11, 117, 110, 0.3);
            background: rgba(11, 117, 110, 0.08);
        }

        .summary-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 14px;
            border: 1px solid rgba(19, 49, 63, 0.12);
            background: linear-gradient(145deg, #e8f2ed, #d7e8e1);
        }

        .summary-title {
            margin: 14px 0 0;
            font-size: 22px;
            letter-spacing: -0.02em;
        }

        .summary-desc {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.7;
            font-size: 14px;
        }

        .price-box {
            margin-top: 16px;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid rgba(19, 49, 63, 0.14);
            background: #f7fbf9;
            display: grid;
            gap: 8px;
        }

        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 14px;
            color: #40606c;
        }

        .row.total {
            border-top: 1px solid rgba(19, 49, 63, 0.12);
            padding-top: 10px;
            color: var(--text);
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .stock-note {
            margin-top: 12px;
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            font-weight: 800;
            color: #0f5448;
            border: 1px solid rgba(15, 84, 72, 0.2);
            background: rgba(226, 247, 237, 0.8);
        }

        @media (max-width: 900px) {
            .wrap {
                grid-template-columns: 1fr;
            }

            .summary-image {
                height: 240px;
            }
        }

        @media (max-width: 680px) {
            body {
                padding: 10px;
            }

            .nav {
                padding: 12px;
                flex-direction: column;
                align-items: stretch;
            }

            .nav-actions {
                display: grid;
                grid-template-columns: 1fr;
            }

            .wrap {
                padding: 14px;
            }

            .panel {
                padding: 14px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .field.full {
                grid-column: auto;
            }
        }

    </style>
</head>
<body>
    <?php
        $name = (string) $product['name'];
        $description = trim((string) $product['description']) !== ''
            ? (string) $product['description']
            : 'Premium quality product crafted for reliability and everyday convenience.';
        $price = (float) $product['price'];
        $stock = (int) $product['stock'];
        $image = trim((string) $product['image']);
        $imageUrl = $image !== '' ? base_url($image) : '';
    ?>

    <main class="shell" role="main">
        <header class="nav" role="banner">
            <a class="brand" href="<?php echo site_url('user'); ?>" aria-label="Store home">
                <span class="brand-mark" aria-hidden="true">EC</span>
                <span class="brand-copy">
                    <span class="brand-title">Ecom Nova</span>
                    <span class="brand-sub">Secure Checkout</span>
                </span>
            </a>

            <div class="nav-actions">
                <a class="nav-chip" href="<?php echo site_url('user'); ?>">Continue Shopping</a>
                <?php if ($is_logged_in): ?>
                    <a class="nav-chip" href="<?php echo ($account_role === 'admin') ? site_url('admin/dashboard') : site_url('user'); ?>">
                        <?php echo html_escape($account_name); ?>
                    </a>
                <?php else: ?>
                    <a class="nav-chip" href="<?php echo site_url('login'); ?>">Sign In</a>
                <?php endif; ?>
            </div>
        </header>

        <section class="wrap">
            <section class="panel" aria-label="Checkout form">
                <h1>Checkout</h1>
                <p class="sub">Choose your quantity and proceed — you will be taken to Stripe's secure hosted checkout to complete payment.</p>

                <?php if (!empty($purchase_notice)): ?>
                    <div class="notice" role="alert"><?php echo html_escape($purchase_notice); ?></div>
                <?php endif; ?>

                <?php if (!empty($is_user_logged_in)): ?>
                    <form method="post" action="<?php echo site_url('checkout/place-order/' . $product['slug']); ?>" autocomplete="on">
                        <div class="form-grid">
                            <div class="field">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" placeholder="John" required>
                            </div>
                            <div class="field">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" placeholder="Doe" required>
                            </div>
                            <div class="field full">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" placeholder="you@example.com" value="<?php echo html_escape((string) $account_email); ?>" required>
                            </div>
                            <div class="field full">
                                <label for="phone">Phone</label>
                                <input type="text" id="phone" name="phone" placeholder="+1 555 123 4567">
                            </div>
                            <div class="field full">
                                <label for="address_line1">Address Line 1</label>
                                <input type="text" id="address_line1" name="address_line1" placeholder="123 Main St" required>
                            </div>
                            <div class="field full">
                                <label for="address_line2">Address Line 2 (Optional)</label>
                                <input type="text" id="address_line2" name="address_line2" placeholder="Apartment, suite, unit, building, floor">
                            </div>
                            <div class="field">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" placeholder="San Francisco" required>
                            </div>
                            <div class="field">
                                <label for="state">State/Province</label>
                                <input type="text" id="state" name="state" placeholder="CA" required>
                            </div>
                            <div class="field">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" id="postal_code" name="postal_code" placeholder="94107" required>
                            </div>
                            <div class="field">
                                <label for="country">Country (ISO2)</label>
                                <input type="text" id="country" name="country" placeholder="US" minlength="2" maxlength="2" required>
                            </div>
                            <div class="field">
                                <label for="quantity">Quantity</label>
                                <select id="quantity" name="quantity">
                                    <?php for ($i = 1; $i <= min(5, $stock); $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="checkout-actions">
                            <a class="btn btn-secondary" href="<?php echo site_url('user'); ?>">Back to Shopping</a>
                            <button class="btn btn-primary" type="submit">Proceed to Checkout</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="login-gate">
                        <p>Checkout is available only for customer accounts. Please log in as a user before placing this order.</p>
                        <div class="checkout-actions">
                            <a class="btn btn-secondary" href="<?php echo site_url('user'); ?>">Back to Catalog</a>
                            <a class="btn btn-primary" href="<?php echo site_url('login'); ?>">Login to Continue</a>
                        </div>
                    </div>
                <?php endif; ?>
            </section>

            <aside class="panel" aria-label="Order summary">
                <?php if ($imageUrl !== ''): ?>
                    <img class="summary-image" src="<?php echo html_escape($imageUrl); ?>" alt="<?php echo html_escape($name); ?>">
                <?php else: ?>
                    <div class="summary-image"></div>
                <?php endif; ?>

                <h2 class="summary-title"><?php echo html_escape($name); ?></h2>
                <p class="summary-desc"><?php echo html_escape($description); ?></p>

                <span class="stock-note">In Stock: <?php echo $stock; ?></span>

                <div class="price-box">
                    <div class="row">
                        <span>Product Price</span>
                        <strong>$<?php echo number_format($price, 2); ?></strong>
                    </div>
                    <div class="row">
                        <span>Shipping</span>
                        <strong>$0.00</strong>
                    </div>
                    <div class="row total">
                        <span>Total</span>
                        <span>$<?php echo number_format($price, 2); ?></span>
                    </div>
                </div>
            </aside>
        </section>
    </main>
</body>
</html>