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
                    <li class="nav-item"><a class="nav-link active" href="<?php echo site_url('admin/users'); ?>">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/products'); ?>">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/orders'); ?>">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/logout'); ?>">Sign Out</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4" role="main">
        <div class="mb-4">
            <h1 class="h3 fw-bold">User Management</h1>
            <p class="text-muted mb-0">Create platform users with role and status controls.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-uppercase fw-bold small text-muted mb-3">Create New User</h5>

                        <?php if (!empty($form_error)): ?>
                            <div class="alert alert-danger"><?php echo $form_error; ?></div>
                        <?php endif; ?>

                        <?php if (!empty($form_success)): ?>
                            <div class="alert alert-success"><?php echo html_escape($form_success); ?></div>
                        <?php endif; ?>

                        <?php
                            $old = isset($old_input) && is_array($old_input) ? $old_input : array();
                            $oldName = isset($old['name']) ? $old['name'] : '';
                            $oldEmail = isset($old['email']) ? $old['email'] : '';
                            $oldRole = isset($old['role']) ? $old['role'] : 'user';
                            $oldPhone = isset($old['phone']) ? $old['phone'] : '';
                            $oldStatus = isset($old['status']) ? $old['status'] : '1';
                        ?>

                        <?php echo form_open('admin/users/create', array('autocomplete' => 'off')); ?>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="name" class="form-label fw-semibold small text-uppercase">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" maxlength="100" required value="<?php echo html_escape($oldName); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold small text-uppercase">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" maxlength="150" required value="<?php echo html_escape($oldEmail); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold small text-uppercase">Temporary Password</label>
                                    <input type="password" class="form-control" id="password" name="password" minlength="8" maxlength="255" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="role" class="form-label fw-semibold small text-uppercase">Role</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="user" <?php echo $oldRole === 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?php echo $oldRole === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-semibold small text-uppercase">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="1" <?php echo $oldStatus === '1' ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo $oldStatus === '0' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="phone" class="form-label fw-semibold small text-uppercase">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" maxlength="20" value="<?php echo html_escape($oldPhone); ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3 fw-bold">Create User Account</button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-uppercase fw-bold small text-muted mb-3">Recently Added Users</h5>

                        <?php if (empty($recent_users)): ?>
                            <p class="text-muted mb-0">No users found yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase fw-bold">Name</th>
                                            <th class="small text-uppercase fw-bold">Role</th>
                                            <th class="small text-uppercase fw-bold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_users as $user): ?>
                                            <tr>
                                                <td><?php echo html_escape($user['name']); ?><br><small class="text-muted"><?php echo html_escape($user['email']); ?></small></td>
                                                <td><?php echo strtoupper(html_escape($user['role'])); ?></td>
                                                <td>
                                                    <?php if ((int) $user['status'] === 1): ?>
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
