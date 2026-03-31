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
<body class="bg-light">

    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo site_url('admin/dashboard'); ?>">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav ms-auto mb-2 mb-md-0">
                    <li class="nav-item"><a class="nav-link active" href="<?php echo site_url('admin/dashboard'); ?>">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/users'); ?>">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/products'); ?>">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/orders'); ?>">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/logout'); ?>">Sign Out</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4" role="main">
        <div class="mb-4">
            <h1 class="h3 fw-bold">Admin Dashboard</h1>
            <p class="text-muted mb-0">Overview of platform activity, team access, and commerce operations.</p>
        </div>

        <div class="row g-3">
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-uppercase text-muted small fw-bold">Total Users</h6>
                        <p class="display-4 fw-bold text-primary mb-1"><?php echo (int) $total_users; ?></p>
                        <p class="text-muted small mb-0">Registered accounts across the platform.</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-uppercase text-muted small fw-bold">Total Products</h6>
                        <p class="display-4 fw-bold text-primary mb-1"><?php echo (int) $total_products; ?></p>
                        <p class="text-muted small mb-0">Products available in the catalog.</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-uppercase text-muted small fw-bold">Total Orders</h6>
                        <p class="display-4 fw-bold text-primary mb-1"><?php echo (int) $total_orders; ?></p>
                        <p class="text-muted small mb-0">Orders captured in the system.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
