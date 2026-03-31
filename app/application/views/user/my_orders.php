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
    <link rel="stylesheet" href="<?php echo base_url('assets/css/user_my_orders.css'); ?>">
</head>
<body>
    <main class="shell" role="main">
        <?php $this->load->view('components/store_nav'); ?>

        <header class="head">
            <div>
                <h1>My Orders</h1>
                <p><?php echo html_escape((string) $account_name); ?><?php echo $account_email !== '' ? ' • ' . html_escape((string) $account_email) : ''; ?></p>
            </div>
        </header>

        <section class="content">
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <?php
                        $createdAt = isset($order['created_at']) ? (string) $order['created_at'] : '';
                        $createdLabel = $createdAt !== '' ? date('d M Y, h:i A', strtotime($createdAt)) : 'N/A';
                        $status = strtoupper((string) ($order['status'] ?? 'pending'));
                        $shippingName = trim((string) ($order['shipping_name'] ?? ''));
                        $shippingAddress = trim((string) ($order['shipping_address'] ?? ''));
                        $shippingCity = trim((string) ($order['shipping_city'] ?? ''));
                        $shippingState = trim((string) ($order['shipping_state'] ?? ''));
                        $shippingPincode = trim((string) ($order['shipping_pincode'] ?? ''));
                    ?>
                    <article class="order">
                        <div class="order-top">
                            <div>
                                <p class="order-id">Order #<?php echo (int) $order['id']; ?></p>
                                <p class="order-date">Placed on <?php echo html_escape($createdLabel); ?></p>
                            </div>
                            <span class="status"><?php echo html_escape($status); ?></span>
                        </div>

                        <div class="order-body">
                            <div class="items">
                                <?php if (!empty($order['items'])): ?>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div class="item">
                                            <div>
                                                <p class="item-name"><?php echo html_escape((string) $item['product_name']); ?></p>
                                                <p class="item-meta">Qty: <?php echo (int) $item['quantity']; ?></p>
                                            </div>
                                            <strong>$<?php echo number_format((float) $item['price'], 2); ?></strong>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="item">
                                        <p class="item-meta">No items found for this order.</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <aside class="summary">
                                <p>Payment: <?php echo html_escape((string) ($order['payment_method'] ?? '-')); ?></p>
                                <div class="amount">$<?php echo number_format((float) ($order['total_amount'] ?? 0), 2); ?></div>
                                <a class="invoice-link" href="<?php echo site_url('my-orders/invoice/' . (int) $order['id']); ?>">View Invoice</a>
                                <a class="receipt-link" href="<?php echo site_url('my-orders/receipt/' . (int) $order['id']); ?>">View Receipt</a>

                                <?php if ($shippingAddress !== '' || $shippingCity !== '' || $shippingState !== ''): ?>
                                    <div class="ship">
                                        <p><strong>Ship To:</strong> <?php echo html_escape($shippingName !== '' ? $shippingName : 'N/A'); ?></p>
                                        <p><?php echo html_escape($shippingAddress); ?></p>
                                        <p><?php echo html_escape(trim($shippingCity . ', ' . $shippingState . ' ' . $shippingPincode)); ?></p>
                                    </div>
                                <?php endif; ?>
                            </aside>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty">
                    You have not placed any orders yet.<br>
                    Browse products and place your first order.
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
