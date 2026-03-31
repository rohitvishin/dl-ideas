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
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/dashboard'); ?>">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/users'); ?>">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/products'); ?>">Products</a></li>
                    <li class="nav-item"><a class="nav-link active" href="<?php echo site_url('admin/orders'); ?>">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/logout'); ?>">Sign Out</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4" role="main">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1">Order List</h1>
                <p class="text-muted mb-0">
                    <?php echo html_escape((string) $admin_name); ?><?php echo $admin_email !== '' ? ' &middot; ' . html_escape((string) $admin_email) : ''; ?>
                </p>
            </div>
        </div>

        <?php if (!empty($orders)): ?>
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
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
                                        <small class="text-muted"><?php echo html_escape((string) ($order['payment_method'] ?? '-')); ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo html_escape((string) ($order['user_name'] ?? 'Unknown')); ?></strong><br>
                                        <small class="text-muted"><?php echo html_escape((string) ($order['user_email'] ?? '')); ?></small>
                                    </td>
                                    <td>
                                        <?php if (!empty($items)): ?>
                                            <ul class="list-unstyled mb-0 small">
                                                <?php foreach ($items as $item): ?>
                                                    <li>
                                                        <?php echo html_escape((string) $item['product_name']); ?>
                                                        <span class="text-muted">(x<?php echo (int) $item['quantity']; ?>)</span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <span class="text-muted">No items</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="fw-bold text-success fs-6">$<?php echo number_format((float) ($order['total_amount'] ?? 0), 2); ?></span></td>
                                    <td><span class="badge bg-success-subtle text-success-emphasis rounded-pill"><?php echo html_escape((string) ($order['status'] ?? 'pending')); ?></span></td>
                                    <td>
                                        <strong><?php echo html_escape((string) ($order['shipping_name'] ?? 'N/A')); ?></strong><br>
                                        <small class="text-muted">
                                            <?php echo html_escape(trim((string) ($order['shipping_city'] ?? '') . ', ' . (string) ($order['shipping_state'] ?? '') . ' ' . (string) ($order['shipping_pincode'] ?? ''))); ?>
                                        </small>
                                    </td>
                                    <td><?php echo html_escape($createdLabel); ?></td>
                                    <td class="text-nowrap">
                                        <a class="btn btn-sm btn-primary mb-1" href="<?php echo site_url('admin/orders/invoice/' . (int) $order['id']); ?>">Invoice</a>
                                        <a class="btn btn-sm btn-outline-primary" href="<?php echo site_url('admin/orders/receipt/' . (int) $order['id']); ?>">Receipt</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <p class="text-muted mb-0">No orders found yet.</p>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
