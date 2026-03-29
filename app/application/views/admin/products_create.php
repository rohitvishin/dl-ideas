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
            --bg: #eef3f7;
            --panel: #ffffff;
            --text: #0f2d3a;
            --muted: #5b707d;
            --border: rgba(15, 45, 58, 0.13);
            --accent: #0b7a75;
            --accent-dark: #085b57;
            --danger-bg: #fff1f0;
            --danger-text: #8e2d2a;
            --success-bg: #ecfdf2;
            --success-text: #216b3f;
            --shadow: 0 20px 44px rgba(13, 35, 51, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--text);
            background: radial-gradient(circle at 8% 8%, rgba(11, 122, 117, 0.18), transparent 44%),
                        linear-gradient(145deg, #f5f9fd, var(--bg));
            min-height: 100vh;
            padding: 22px;
        }

        .shell {
            max-width: 1180px;
            margin: 0 auto;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            padding: 24px 28px;
            background: linear-gradient(145deg, #103f5b, #0b7a75);
            color: #f2fbfb;
        }

        .topbar h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: -0.01em;
        }

        .topbar p {
            margin: 4px 0 0;
            color: rgba(242, 251, 251, 0.9);
            font-size: 14px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .btn-outline {
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.45);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .layout {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 20px;
            padding: 24px;
        }

        .panel {
            background: #fbfdff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px;
        }

        .panel h2 {
            margin: 0 0 14px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #1f4758;
        }

        .alert {
            margin-bottom: 14px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.45;
        }

        .alert-danger {
            background: var(--danger-bg);
            color: var(--danger-text);
            border: 1px solid rgba(168, 61, 55, 0.26);
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success-text);
            border: 1px solid rgba(50, 140, 90, 0.25);
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .field {
            margin-bottom: 14px;
        }

        .field.full {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #315566;
            font-weight: 800;
        }

        input,
        textarea,
        select {
            width: 100%;
            border: 1px solid rgba(24, 62, 82, 0.28);
            border-radius: 10px;
            font-size: 14px;
            padding: 11px 12px;
            background: #ffffff;
            color: var(--text);
            outline: none;
            transition: border-color 160ms ease, box-shadow 160ms ease;
        }

        textarea {
            min-height: 96px;
            resize: vertical;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: rgba(11, 122, 117, 0.75);
            box-shadow: 0 0 0 4px rgba(11, 122, 117, 0.13);
        }

        .submit {
            margin-top: 4px;
            border: 0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 800;
            color: #ffffff;
            background: linear-gradient(145deg, var(--accent), #0b5d88);
            cursor: pointer;
            transition: transform 150ms ease;
        }

        .submit:hover {
            transform: translateY(-1px);
            background: linear-gradient(145deg, var(--accent-dark), #084e74);
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            font-size: 13px;
            padding: 10px 8px;
            border-bottom: 1px solid rgba(16, 44, 58, 0.1);
            white-space: nowrap;
        }

        th {
            color: #2f5566;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pill {
            display: inline-block;
            padding: 5px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .pill.active {
            background: #e9fdf2;
            color: #1f7f4a;
        }

        .pill.inactive {
            background: #fff1f0;
            color: #a3403a;
        }

        .empty {
            margin: 0;
            font-size: 14px;
            color: var(--muted);
        }

        @media (max-width: 980px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .field.full {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>
    <main class="shell" role="main">
        <header class="topbar">
            <div>
                <h1>Product Management</h1>
                <p>Create products with pricing, stock, status, and catalog metadata.</p>
            </div>
            <div class="actions">
                <a class="btn btn-outline" href="<?php echo site_url('admin/dashboard'); ?>">Back to Dashboard</a>
                <a class="btn btn-outline" href="<?php echo site_url('admin/logout'); ?>">Sign Out</a>
            </div>
        </header>

        <section class="layout">
            <article class="panel">
                <h2>Add New Product</h2>

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
                    <div class="grid">
                        <div class="field full">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" maxlength="150" required value="<?php echo html_escape($oldName); ?>">
                        </div>

                        <div class="field">
                            <label for="slug">Slug (optional)</label>
                            <input type="text" id="slug" name="slug" maxlength="150" placeholder="auto-generated-if-empty" value="<?php echo html_escape($oldSlug); ?>">
                        </div>

                        <div class="field">
                            <label for="image">Product Image</label>
                            <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp,.gif">
                        </div>

                        <div class="field full">
                            <label for="description">Description</label>
                            <textarea id="description" name="description"><?php echo html_escape($oldDesc); ?></textarea>
                        </div>

                        <div class="field">
                            <label for="price">Price</label>
                            <input type="number" id="price" name="price" min="0.01" step="0.01" required value="<?php echo html_escape($oldPrice); ?>">
                        </div>

                        <div class="field">
                            <label for="stock">Stock</label>
                            <input type="number" id="stock" name="stock" min="0" step="1" required value="<?php echo html_escape($oldStock); ?>">
                        </div>

                        <div class="field">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="1" <?php echo $oldStatus === '1' ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo $oldStatus === '0' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="submit">Create Product</button>
                <?php echo form_close(); ?>
            </article>

            <aside class="panel">
                <h2>Recent Products</h2>

                <?php if (empty($recent_products)): ?>
                    <p class="empty">No products found yet.</p>
                <?php else: ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_products as $product): ?>
                                    <tr>
                                        <td><?php echo html_escape($product['name']); ?><br><span style="color:#6a7f8a; font-size:12px;"><?php echo html_escape($product['slug']); ?></span></td>
                                        <td><?php echo html_escape($product['price']); ?></td>
                                        <td><?php echo (int) $product['stock']; ?></td>
                                        <td>
                                            <?php if ((int) $product['status'] === 1): ?>
                                                <span class="pill active">Active</span>
                                            <?php else: ?>
                                                <span class="pill inactive">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </aside>
        </section>
    </main>
</body>
</html>
