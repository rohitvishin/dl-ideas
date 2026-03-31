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
    <link rel="stylesheet" href="<?php echo base_url('assets/css/user_payment_failure.css'); ?>">
</head>
<body>
    <main class="shell" role="main">
        <?php $this->load->view('components/store_nav'); ?>

        <section class="card">
            <h1>Payment Failed</h1>
            <p>We could not complete your payment. You can try checkout again or return to the product catalog.</p>

            <div class="actions">
                <?php if (!empty($product_slug)): ?>
                    <a class="btn btn-primary" href="<?php echo site_url('buy-now/'.$product_slug); ?>">Try Again</a>
                <?php endif; ?>
                <a class="btn btn-secondary" href="<?php echo site_url('user'); ?>">Back to Catalog</a>
            </div>
        </section>
    </main>
</body>
</html>
