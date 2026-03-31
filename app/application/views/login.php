<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo html_escape($title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="card shadow-lg border-0 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-6 text-white bg-dark bg-gradient d-flex flex-column justify-content-center p-4 p-lg-5">
                            <span class="badge bg-light bg-opacity-25 text-white rounded-pill px-3 py-2 mb-3 align-self-start text-uppercase fw-bold small">Platform Login</span>
                            <h1 class="fw-bold mb-3">Secure access for administrators and users.</h1>
                            <p class="text-white-50 mb-0">Sign in with your credentials. Admin accounts open the dashboard, while user accounts go to the product listing.</p>
                        </div>

                        <div class="col-md-6 p-4 p-lg-5">
                            <h2 class="fw-bold mb-1">Welcome back</h2>
                            <p class="text-muted mb-4">Use your registered email and password to continue.</p>

                            <?php if ($this->session->flashdata('auth_error')): ?>
                                <div class="alert alert-danger"><?php echo html_escape($this->session->flashdata('auth_error')); ?></div>
                            <?php endif; ?>

                            <?php if ($this->session->flashdata('auth_success')): ?>
                                <div class="alert alert-success"><?php echo html_escape($this->session->flashdata('auth_success')); ?></div>
                            <?php endif; ?>

                            <?php if (validation_errors()): ?>
                                <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
                            <?php endif; ?>

                            <?php echo form_open('authenticate', array('autocomplete' => 'off')); ?>
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold small text-uppercase">Email Address</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?php echo set_value('email'); ?>" placeholder="name@company.com" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold small text-uppercase">Password</label>
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Enter your password" required>
                                </div>

                                <button type="submit" class="btn btn-dark btn-lg w-100">Sign In</button>
                            <?php echo form_close(); ?>

                            <p class="text-center text-muted small mt-3 mb-0">Protected area. Authorized accounts only.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
