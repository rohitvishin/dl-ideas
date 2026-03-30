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
            --ink: #132f3f;
            --muted: #637983;
            --brand: #0b766f;
            --brand-strong: #065b56;
            --line: rgba(19, 47, 63, 0.16);
            --paper: #ffffff;
            --bg: #eef5f2;
            --shadow: 0 20px 40px rgba(10, 34, 43, 0.1);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background: radial-gradient(circle at 0% 0%, rgba(11, 118, 111, 0.18), transparent 33%),
                        linear-gradient(150deg, #f7fbf9, var(--bg));
            padding: 24px;
        }

        .top {
            max-width: 880px;
            margin: 0 auto 12px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            border-radius: 999px;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 700;
            border: 1px solid rgba(6, 91, 86, 0.28);
            color: var(--brand-strong);
            background: #eef9f7;
        }

        .btn.print {
            color: #ffffff;
            background: linear-gradient(145deg, var(--brand), #15856b);
            border-color: transparent;
        }

        .receipt {
            max-width: 880px;
            margin: 0 auto;
            border-radius: 18px;
            overflow: hidden;
            background: var(--paper);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
        }

        .head {
            padding: 22px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            background: linear-gradient(145deg, #0f3e58, #0b766f);
            color: #ecf8f8;
        }

        .head h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: -0.03em;
        }

        .head p {
            margin: 6px 0 0;
            font-size: 13px;
            color: rgba(236, 248, 248, 0.82);
        }

        .meta {
            text-align: right;
            font-size: 13px;
            line-height: 1.8;
        }

        .body {
            padding: 18px;
            display: grid;
            gap: 12px;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            background: #fbfdfd;
        }

        .card h2 {
            margin: 0 0 6px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .card p {
            margin: 0;
            font-size: 14px;
            line-height: 1.7;
        }

        .summary {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            background: #fbfdfc;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 6px 0;
            color: var(--muted);
            font-size: 14px;
        }

        .row.total {
            margin-top: 6px;
            border-top: 1px dashed var(--line);
            padding-top: 10px;
            font-size: 18px;
            font-weight: 800;
            color: var(--ink);
        }

        .foot {
            border-top: 1px solid var(--line);
            padding: 12px 18px 16px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            color: var(--muted);
            font-size: 12px;
        }

        .badge {
            display: inline-flex;
            border-radius: 999px;
            padding: 4px 10px;
            border: 1px solid rgba(11, 118, 111, 0.24);
            background: rgba(11, 118, 111, 0.12);
            color: var(--brand-strong);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        @media (max-width: 760px) {
            body { padding: 12px; }
            .meta { text-align: left; }
        }

        @media print {
            body { background: #fff; padding: 0; }
            .top { display: none; }
            .receipt { border: 0; box-shadow: none; border-radius: 0; max-width: none; }
        }
    </style>
</head>
<body>
    <?php
        $createdAt = isset($order['created_at']) ? (string) $order['created_at'] : '';
        $receiptDate = $createdAt !== '' ? date('d M Y', strtotime($createdAt)) : date('d M Y');
        $status = strtoupper((string) ($order['status'] ?? 'pending'));
        $total = (float) ($order['total_amount'] ?? 0);

        $backUrl = $viewer_role === 'admin' ? site_url('admin/orders') : site_url('my-orders');
        $paymentMethod = (string) ($order['payment_method'] ?? '-');
        $paymentIntentId = trim((string) ($order['stripe_payment_intent_id'] ?? ''));
        $chargeId = trim((string) ($order['stripe_charge_id'] ?? ''));
        $sessionId = trim((string) ($order['stripe_session_id'] ?? ''));
        $transactionId = $chargeId !== '' ? $chargeId : ($paymentIntentId !== '' ? $paymentIntentId : $sessionId);
    ?>

    <div class="top">
        <a class="btn" href="<?php echo $backUrl; ?>">Back to Orders</a>
        <a class="btn print" href="#" onclick="window.print(); return false;">Print Receipt</a>
    </div>

    <main class="receipt" role="main">
        <header class="head">
            <div>
                <h1>Payment Receipt</h1>
                <p>Ecom Nova</p>
            </div>
            <div class="meta">
                <div><strong>Receipt #</strong> RCPT-<?php echo str_pad((string) ((int) $order['id']), 6, '0', STR_PAD_LEFT); ?></div>
                <div><strong>Date</strong> <?php echo html_escape($receiptDate); ?></div>
                <div><strong>Order #</strong> <?php echo (int) $order['id']; ?></div>
            </div>
        </header>

        <section class="body">
            <article class="card">
                <h2>Transaction Details</h2>
                <p>
                    This receipt contains transaction-only information for accounting reference.
                </p>
            </article>

            <section class="summary">
                <div class="row"><span>Order ID</span><strong>#<?php echo (int) $order['id']; ?></strong></div>
                <div class="row"><span>Receipt Date</span><strong><?php echo html_escape($receiptDate); ?></strong></div>
                <div class="row"><span>Payment Method</span><strong><?php echo html_escape($paymentMethod); ?></strong></div>
                <div class="row"><span>Transaction ID</span><strong><?php echo html_escape($transactionId !== '' ? $transactionId : 'N/A'); ?></strong></div>
                <div class="row"><span>Payment Intent ID</span><strong><?php echo html_escape($paymentIntentId !== '' ? $paymentIntentId : 'N/A'); ?></strong></div>
                <div class="row"><span>Charge ID</span><strong><?php echo html_escape($chargeId !== '' ? $chargeId : 'N/A'); ?></strong></div>
                <div class="row"><span>Transaction Status</span><strong><?php echo html_escape($status); ?></strong></div>
                <div class="row total"><span>Total Paid</span><span>$<?php echo number_format($total, 2); ?></span></div>
            </section>
        </section>

        <footer class="foot">
            <span>Generated by <?php echo html_escape((string) $viewer_name); ?></span>
            <span class="badge"><?php echo html_escape($status); ?></span>
        </footer>
    </main>
</body>
</html>
