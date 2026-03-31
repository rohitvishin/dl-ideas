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
        <?php
            $this->load->view('components/store_nav', array(
                'nav_brand_tag' => 'My Orders',
                'nav_show_support' => false,
                'nav_show_account_dropdown' => false,
                'nav_links' => array(
                    array('label' => 'Back to Catalog', 'url' => site_url('user')),
                    array('label' => 'Logout', 'url' => site_url('logout')),
                ),
            ));
        ?>

        <header class="head">
            <div>
                <h1>My Orders</h1>
                <p><?php echo html_escape((string) $account_name); ?><?php echo $account_email !== '' ? ' • ' . html_escape((string) $account_email) : ''; ?></p>
            </div>
        </header>

        <section class="content">
            <?php if (!empty($orders)): ?>
                <div class="table-wrap">
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Payment</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <?php
                                    $createdAt = isset($order['created_at']) ? (string) $order['created_at'] : '';
                                    $createdLabel = $createdAt !== '' ? date('d M Y', strtotime($createdAt)) : 'N/A';
                                    $status = strtoupper((string) ($order['status'] ?? 'pending'));
                                    $itemNames = array();
                                    if (!empty($order['items'])) {
                                        foreach ($order['items'] as $item) {
                                            $itemNames[] = html_escape((string) $item['product_name']) . ' &times; ' . (int) $item['quantity'];
                                        }
                                    }
                                ?>
                                <tr>
                                    <td><span class="order-id">#<?php echo (int) $order['id']; ?></span></td>
                                    <td class="order-date"><?php echo html_escape($createdLabel); ?></td>
                                    <td class="order-items"><?php echo !empty($itemNames) ? implode('<br>', $itemNames) : '<span class="muted">—</span>'; ?></td>
                                    <td><?php echo html_escape((string) ($order['payment_method'] ?? '-')); ?></td>
                                    <td><span class="amount">$<?php echo number_format((float) ($order['total_amount'] ?? 0), 2); ?></span></td>
                                    <td><span class="status"><?php echo html_escape($status); ?></span></td>
                                    <td class="actions-cell">
                                        <a class="invoice-link" href="<?php echo site_url('my-orders/invoice/' . (int) $order['id']); ?>">Invoice</a>
                                        <a class="receipt-link" href="<?php echo site_url('my-orders/receipt/' . (int) $order['id']); ?>">Receipt</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
