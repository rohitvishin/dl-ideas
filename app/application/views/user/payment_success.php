<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo html_escape($title); ?></title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f8f6;
            color: #12313f;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
        }
        .card {
            max-width: 640px;
            width: 100%;
            background: #fff;
            border: 1px solid #dce7e2;
            border-radius: 16px;
            box-shadow: 0 16px 30px rgba(16, 39, 48, 0.08);
            padding: 24px;
        }
        h1 {
            margin: 0;
            font-size: 30px;
            color: #0f6a4f;
        }
        p {
            margin: 10px 0 0;
            line-height: 1.7;
            color: #4f6770;
        }
        .actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 11px 14px;
            text-decoration: none;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
        }
        .btn-primary {
            color: #fff;
            background: #0b766f;
        }
        .btn-secondary {
            color: #0b766f;
            border: 1px solid #8dc7c2;
            background: #edf9f7;
        }
        .meta {
            margin-top: 12px;
            font-size: 12px;
            color: #6d828a;
        }
    </style>
</head>
<body>
    <main class="card" role="main">
        <h1>Payment Successful</h1>
        <p>Your payment has been completed successfully. Your order is now being processed.</p>

        <?php if (!empty($session_id)): ?>
            <p class="meta">Stripe Session: <?php echo html_escape($session_id); ?></p>
        <?php endif; ?>

        <div class="actions">
            <a class="btn btn-primary" href="<?php echo site_url('user'); ?>">Back to Catalog</a>
            <a class="btn btn-secondary" href="<?php echo site_url('user'); ?>">Continue Shopping</a>
        </div>
    </main>
</body>
</html>
