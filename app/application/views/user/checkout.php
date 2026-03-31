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
    <link rel="stylesheet" href="<?php echo base_url('assets/css/user_checkout.css'); ?>">
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
        <?php $this->load->view('components/store_nav'); ?>

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