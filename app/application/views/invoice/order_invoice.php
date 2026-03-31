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
            --ink: #153140;
            --muted: #607681;
            --brand: #0b766f;
            --brand-strong: #075e58;
            --paper: #ffffff;
            --line: rgba(21, 49, 64, 0.16);
            --bg: #edf4f2;
            --shadow: 0 24px 50px rgba(11, 35, 44, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 10% 4%, rgba(11, 118, 111, 0.2), transparent 34%),
                radial-gradient(circle at 95% 0%, rgba(242, 186, 103, 0.18), transparent 30%),
                linear-gradient(160deg, #f7fbf9, var(--bg));
            padding: 24px;
        }

        .page-actions {
            max-width: 980px;
            margin: 0 auto 12px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action {
            text-decoration: none;
            border-radius: 999px;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 700;
            border: 1px solid rgba(7, 94, 88, 0.22);
            background: #f2faf8;
            color: var(--brand-strong);
        }

        .action.print {
            background: linear-gradient(145deg, var(--brand), #13866d);
            color: #ffffff;
            border-color: transparent;
        }

        .invoice {
            max-width: 980px;
            margin: 0 auto;
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .header {
            padding: 26px 28px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            color: #ecf8f7;
            background: linear-gradient(145deg, #0f3d56, #0b766f);
        }

        .brand {
            display: grid;
            gap: 4px;
        }

        .brand h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: -0.03em;
        }

        .brand p {
            margin: 0;
            color: rgba(236, 248, 247, 0.82);
            font-size: 13px;
        }

        .meta {
            text-align: right;
            display: grid;
            gap: 6px;
        }

        .meta .label {
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(236, 248, 247, 0.74);
            font-weight: 700;
        }

        .meta .value {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .content {
            padding: 20px 24px 24px;
            display: grid;
            gap: 14px;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px;
            background: #fbfdfd;
        }

        .card h2 {
            margin: 0 0 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .card p {
            margin: 0;
            line-height: 1.7;
            font-size: 14px;
        }

        .items-wrap {
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid var(--line);
            font-size: 13px;
        }

        th {
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 11px;
            font-weight: 800;
            color: var(--muted);
            background: #f6faf9;
        }

        td.right,
        th.right {
            text-align: right;
        }

        .totals {
            margin-left: auto;
            width: 320px;
            max-width: 100%;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            background: #fbfdfc;
        }

        .line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 14px;
            color: var(--muted);
            padding: 6px 0;
        }

        .line.total {
            border-top: 1px dashed var(--line);
            margin-top: 6px;
            padding-top: 10px;
            color: var(--ink);
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .footer {
            border-top: 1px solid var(--line);
            padding: 14px 24px 20px;
            color: var(--muted);
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .status {
            display: inline-block;
            border-radius: 999px;
            padding: 4px 10px;
            border: 1px solid rgba(11, 118, 111, 0.24);
            background: rgba(11, 118, 111, 0.12);
            color: var(--brand-strong);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        @media (max-width: 760px) {
            body {
                padding: 12px;
            }

            .header {
                padding: 18px;
            }

            .content,
            .footer {
                padding-left: 16px;
                padding-right: 16px;
            }

            .row {
                grid-template-columns: 1fr;
            }

            .meta {
                text-align: left;
            }
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .page-actions {
                display: none;
            }

            .invoice {
                border: 0;
                border-radius: 0;
                box-shadow: none;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <?php
        $createdAt = isset($order['created_at']) ? (string) $order['created_at'] : '';
        $invoiceDate = $createdAt !== '' ? date('d M Y', strtotime($createdAt)) : date('d M Y');
        $status = strtoupper((string) ($order['status'] ?? 'pending'));
        $items = isset($order['items']) && is_array($order['items']) ? $order['items'] : array();

        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += ((float) $item['price'] * (int) $item['quantity']);
        }

        $grandTotal = (float) ($order['total_amount'] ?? 0);
        if ($grandTotal <= 0 && $subtotal > 0) {
            $grandTotal = $subtotal;
        }

        $fromLabel = $viewer_role === 'admin' ? site_url('admin/orders') : site_url('my-orders');
        $buyerName = trim((string) ($order['user_name'] ?? $order['shipping_name'] ?? 'Customer'));
        $buyerEmail = trim((string) ($order['user_email'] ?? ''));
        $shipName = trim((string) ($order['shipping_name'] ?? ''));
        $shipPhone = trim((string) ($order['shipping_phone'] ?? ''));
        $shipAddress = trim((string) ($order['shipping_address'] ?? ''));
        $shipCity = trim((string) ($order['shipping_city'] ?? ''));
        $shipState = trim((string) ($order['shipping_state'] ?? ''));
        $shipPincode = trim((string) ($order['shipping_pincode'] ?? ''));
    ?>

    <div class="page-actions">
        <a class="action" href="<?php echo $fromLabel; ?>">Back to Orders</a>
        <a class="action print" href="#" onclick="window.print(); return false;">Print Invoice</a>
    </div>

    <main class="invoice" role="main">
        <header class="header">
            <section class="brand">
                <h1>Ecom Nova Invoice</h1>
                <p>Secure commerce billing statement</p>
            </section>
            <section class="meta">
                <span class="label">Invoice Number</span>
                <span class="value">INV-<?php echo str_pad((string) ((int) $order['id']), 6, '0', STR_PAD_LEFT); ?></span>
                <span class="label">Date: <?php echo html_escape($invoiceDate); ?></span>
            </section>
        </header>

        <section class="content">
            <div class="row">
                <article class="card">
                    <h2>Billed To</h2>
                    <p>
                        <?php echo html_escape($buyerName !== '' ? $buyerName : 'Customer'); ?><br>
                        <?php if ($buyerEmail !== ''): ?>
                            <?php echo html_escape($buyerEmail); ?>
                        <?php else: ?>
                            Not available
                        <?php endif; ?>
                    </p>
                </article>

                <article class="card">
                    <h2>Shipping Address</h2>
                    <p>
                        <?php echo html_escape($shipName !== '' ? $shipName : $buyerName); ?><br>
                        <?php echo html_escape($shipAddress !== '' ? $shipAddress : 'Address unavailable'); ?><br>
                        <?php echo html_escape(trim($shipCity . ', ' . $shipState . ' ' . $shipPincode)); ?><br>
                        <?php if ($shipPhone !== ''): ?>
                            <?php echo html_escape($shipPhone); ?>
                        <?php endif; ?>
                    </p>
                </article>
            </div>

            <section class="items-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th class="right">Qty</th>
                            <th class="right">Unit Price</th>
                            <th class="right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $index => $item): ?>
                                <?php $lineTotal = (float) $item['price'] * (int) $item['quantity']; ?>
                                <tr>
                                    <td><?php echo (int) $index + 1; ?></td>
                                    <td><?php echo html_escape((string) $item['product_name']); ?></td>
                                    <td class="right"><?php echo (int) $item['quantity']; ?></td>
                                    <td class="right">$<?php echo number_format((float) $item['price'], 2); ?></td>
                                    <td class="right">$<?php echo number_format($lineTotal, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No invoice items available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <section class="totals">
                <div class="line"><span>Payment Method</span><strong><?php echo html_escape((string) ($order['payment_method'] ?? '-')); ?></strong></div>
                <div class="line"><span>Subtotal</span><strong>$<?php echo number_format($subtotal, 2); ?></strong></div>
                <div class="line"><span>Tax</span><strong>$0.00</strong></div>
                <div class="line total"><span>Total</span><span>$<?php echo number_format($grandTotal, 2); ?></span></div>
            </section>
        </section>

        <footer class="footer">
            <span>Generated by <?php echo html_escape((string) $viewer_name); ?></span>
            <span class="status"><?php echo html_escape($status); ?></span>
        </footer>
    </main>
</body>
</html>
