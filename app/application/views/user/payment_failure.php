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
            background: #faf5f4;
            color: #3a1f1f;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
        }
        .card {
            max-width: 640px;
            width: 100%;
            background: #fff;
            border: 1px solid #f0d8d5;
            border-radius: 16px;
            box-shadow: 0 16px 30px rgba(56, 28, 28, 0.08);
            padding: 24px;
        }
        h1 {
            margin: 0;
            font-size: 30px;
            color: #9e2f2f;
        }
        p {
            margin: 10px 0 0;
            line-height: 1.7;
            color: #6f4f4f;
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
            background: #9e2f2f;
        }
        .btn-secondary {
            color: #0b766f;
            border: 1px solid #8dc7c2;
            background: #edf9f7;
        }
    </style>
</head>
<body>
    <main class="card" role="main">
        <h1>Payment Failed</h1>
        <p>We could not complete your payment. You can try checkout again or return to the product catalog.</p>

        <div class="actions">
            <?php if (!empty($product_slug)): ?>
                <a class="btn btn-primary" href="<?php echo site_url('buy-now/'.$product_slug); ?>">Try Again</a>
            <?php endif; ?>
            <a class="btn btn-secondary" href="<?php echo site_url('user'); ?>">Back to Catalog</a>
        </div>
    </main>
</body>
</html>
