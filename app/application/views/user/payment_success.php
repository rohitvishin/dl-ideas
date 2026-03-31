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
    <link rel="stylesheet" href="<?php echo base_url('assets/css/user_payment_success.css'); ?>">
</head>
<body>
    <main class="shell" role="main">
        <?php $this->load->view('components/store_nav'); ?>

        <section class="card">
            <h1>Payment Successful</h1>
            <p>Your payment has been completed successfully.</p>

            <div class="actions">
                <a class="btn btn-secondary" href="<?php echo site_url('user'); ?>">Continue Shopping</a>
            </div>
        </section>
    </main>
</body>
</html>
