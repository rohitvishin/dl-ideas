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
                    <li class="nav-item"><a class="nav-link active" href="<?php echo site_url('admin/products'); ?>">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/orders'); ?>">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/logout'); ?>">Sign Out</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4" role="main">
        <div class="mb-4">
            <h1 class="h3 fw-bold">Product Management</h1>
            <p class="text-muted mb-0">Create products with pricing, stock, status, and catalog metadata.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-uppercase fw-bold small text-muted mb-3">Add New Product</h5>

                        <?php if (!empty($form_error)): ?>
                            <div class="alert alert-danger"><?php echo $form_error; ?></div>
                        <?php endif; ?>

                        <?php if (!empty($form_success)): ?>
                            <div class="alert alert-success"><?php echo html_escape($form_success); ?></div>
                        <?php endif; ?>

                        <?php
                            $old = isset($old_input) && is_array($old_input) ? $old_input : array();
                            $oldName = isset($old['name']) ? $old['name'] : '';
                            $oldSlug = isset($old['slug']) ? $old['slug'] : '';
                            $oldDesc = isset($old['description']) ? $old['description'] : '';
                            $oldPrice = isset($old['price']) ? $old['price'] : '';
                            $oldStock = isset($old['stock']) ? $old['stock'] : '0';
                            $oldStatus = isset($old['status']) ? $old['status'] : '1';
                        ?>

                        <?php echo form_open_multipart('admin/products/create', array('autocomplete' => 'off')); ?>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="name" class="form-label fw-semibold small text-uppercase">Product Name</label>
                                    <input type="text" class="form-control" id="name" name="name" maxlength="150" required value="<?php echo html_escape($oldName); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label for="slug" class="form-label fw-semibold small text-uppercase">Slug (optional)</label>
                                    <input type="text" class="form-control" id="slug" name="slug" maxlength="150" placeholder="auto-generated-if-empty" value="<?php echo html_escape($oldSlug); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label for="image" class="form-label fw-semibold small text-uppercase">Product Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept=".jpg,.jpeg,.png,.webp,.gif">
                                </div>

                                <div class="col-12">
                                    <label for="description" class="form-label fw-semibold small text-uppercase">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo html_escape($oldDesc); ?></textarea>
                                </div>

                                <div class="col-md-4">
                                    <label for="price" class="form-label fw-semibold small text-uppercase">Price</label>
                                    <input type="number" class="form-control" id="price" name="price" min="0.01" step="0.01" required value="<?php echo html_escape($oldPrice); ?>">
                                </div>

                                <div class="col-md-4">
                                    <label for="stock" class="form-label fw-semibold small text-uppercase">Stock</label>
                                    <input type="number" class="form-control" id="stock" name="stock" min="0" step="1" required value="<?php echo html_escape($oldStock); ?>">
                                </div>

                                <div class="col-md-4">
                                    <label for="status" class="form-label fw-semibold small text-uppercase">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="1" <?php echo $oldStatus === '1' ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo $oldStatus === '0' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3 fw-bold">Create Product</button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-uppercase fw-bold small text-muted mb-3">Recent Products</h5>

                        <?php if (empty($recent_products)): ?>
                            <p class="text-muted mb-0">No products found yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase fw-bold">Product</th>
                                            <th class="small text-uppercase fw-bold">Price</th>
                                            <th class="small text-uppercase fw-bold">Stock</th>
                                            <th class="small text-uppercase fw-bold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_products as $product): ?>
                                            <tr>
                                                <td><?php echo html_escape($product['name']); ?><br><small class="text-muted"><?php echo html_escape($product['slug']); ?></small></td>
                                                <td><?php echo html_escape($product['price']); ?></td>
                                                <td><?php echo (int) $product['stock']; ?></td>
                                                <td>
                                                    <?php if ((int) $product['status'] === 1): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
