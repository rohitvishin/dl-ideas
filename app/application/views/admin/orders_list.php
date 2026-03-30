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
            --bg: #edf3f7;
            --panel: #ffffff;
            --text: #102c3a;
            --muted: #607480;
            --accent: #0b7a75;
            --accent-strong: #075f5a;
            --border: rgba(16, 44, 58, 0.14);
            --shadow: 0 20px 46px rgba(10, 42, 58, 0.12);
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
            background: radial-gradient(circle at 6% 6%, rgba(11, 122, 117, 0.16), transparent 38%),
                        linear-gradient(150deg, #f7fbfd, var(--bg));
            padding: 24px;
        }

        .shell {
            max-width: 1240px;
            margin: 0 auto;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid var(--border);
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .header {
            padding: 24px;
            color: #eef9fa;
            background: linear-gradient(145deg, #0f3e58, #0b7a75);
        }

        .header h1 {
            margin: 0;
            font-size: clamp(26px, 3.8vw, 36px);
            letter-spacing: -0.03em;
        }

        .header p {
            margin: 8px 0 0;
            font-size: 14px;
            color: rgba(238, 249, 250, 0.84);
        }

        .nav {
            margin-top: 16px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-link {
            text-decoration: none;
            color: #f4fcfd;
            border: 1px solid rgba(255, 255, 255, 0.34);
            border-radius: 10px;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link.is-active {
            background: rgba(255, 255, 255, 0.2);
        }

        .content {
            padding: 18px;
        }

        .empty {
            border: 1px dashed rgba(16, 44, 58, 0.26);
            border-radius: 14px;
            padding: 28px;
            text-align: center;
            color: var(--muted);
            line-height: 1.7;
            background: #fbfdff;
        }

        .table-wrap {
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }

        th,
        td {
            text-align: left;
            vertical-align: top;
            padding: 12px;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
        }

        th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--muted);
            background: #f6fafc;
            font-weight: 800;
        }

        tbody tr:hover {
            background: #f8fcfb;
        }

        .status {
            display: inline-block;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--accent-strong);
            background: rgba(11, 122, 117, 0.12);
            border: 1px solid rgba(11, 122, 117, 0.2);
        }

        .items {
            margin: 0;
            padding-left: 18px;
            display: grid;
            gap: 4px;
            color: #244653;
        }

        .price {
            font-weight: 800;
            color: var(--accent-strong);
            font-size: 16px;
            letter-spacing: -0.01em;
        }

        .muted {
            color: var(--muted);
        }

        .invoice-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 8px 11px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 800;
            text-decoration: none;
            color: #ffffff;
            background: linear-gradient(145deg, var(--accent), #16856c);
        }

        .receipt-link {
            margin-top: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 800;
            text-decoration: none;
            color: #0b5f58;
            border: 1px solid rgba(11, 122, 117, 0.3);
            background: #e9f7f5;
        }

        @media (max-width: 780px) {
            body {
                padding: 12px;
            }

            .header,
            .content {
                padding: 16px;
            }

            .nav {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }

            .nav-link {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <main class="shell" role="main">
        <header class="header">
            <h1>Order List</h1>
            <p><?php echo html_escape((string) $admin_name); ?><?php echo $admin_email !== '' ? ' • ' . html_escape((string) $admin_email) : ''; ?></p>

            <nav class="nav" aria-label="Admin navigation">
                <a class="nav-link" href="<?php echo site_url('admin/dashboard'); ?>">Dashboard</a>
                <a class="nav-link" href="<?php echo site_url('admin/users'); ?>">Users</a>
                <a class="nav-link" href="<?php echo site_url('admin/products'); ?>">Products</a>
                <a class="nav-link is-active" href="<?php echo site_url('admin/orders'); ?>">Orders</a>
                <a class="nav-link" href="<?php echo site_url('admin/logout'); ?>">Sign Out</a>
            </nav>
        </header>

        <section class="content">
            <?php if (!empty($orders)): ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>User</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Shipping</th>
                                <th>Placed At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <?php
                                    $createdAt = isset($order['created_at']) ? (string) $order['created_at'] : '';
                                    $createdLabel = $createdAt !== '' ? date('d M Y, h:i A', strtotime($createdAt)) : 'N/A';
                                    $items = isset($order['items']) && is_array($order['items']) ? $order['items'] : array();
                                ?>
                                <tr>
                                    <td>
                                        <strong>#<?php echo (int) $order['id']; ?></strong><br>
                                        <span class="muted"><?php echo html_escape((string) ($order['payment_method'] ?? '-')); ?></span>
                                    </td>
                                    <td>
                                        <strong><?php echo html_escape((string) ($order['user_name'] ?? 'Unknown')); ?></strong><br>
                                        <span class="muted"><?php echo html_escape((string) ($order['user_email'] ?? '')); ?></span>
                                    </td>
                                    <td>
                                        <?php if (!empty($items)): ?>
                                            <ul class="items">
                                                <?php foreach ($items as $item): ?>
                                                    <li>
                                                        <?php echo html_escape((string) $item['product_name']); ?>
                                                        <span class="muted">(x<?php echo (int) $item['quantity']; ?>)</span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <span class="muted">No items</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="price">$<?php echo number_format((float) ($order['total_amount'] ?? 0), 2); ?></span></td>
                                    <td><span class="status"><?php echo html_escape((string) ($order['status'] ?? 'pending')); ?></span></td>
                                    <td>
                                        <strong><?php echo html_escape((string) ($order['shipping_name'] ?? 'N/A')); ?></strong><br>
                                        <span class="muted">
                                            <?php echo html_escape(trim((string) ($order['shipping_city'] ?? '') . ', ' . (string) ($order['shipping_state'] ?? '') . ' ' . (string) ($order['shipping_pincode'] ?? ''))); ?>
                                        </span>
                                    </td>
                                    <td><?php echo html_escape($createdLabel); ?></td>
                                    <td>
                                        <a class="invoice-link" href="<?php echo site_url('admin/orders/invoice/' . (int) $order['id']); ?>">View Invoice</a>
                                        <a class="receipt-link" href="<?php echo site_url('admin/orders/receipt/' . (int) $order['id']); ?>">View Receipt</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty">No orders found yet.</div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
