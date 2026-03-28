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
            --text: #102c3a;
            --muted: #5b707d;
            --accent: #0b7a75;
            --accent-strong: #065b57;
            --nav-bg: #0d3951;
            --border: rgba(16, 44, 58, 0.12);
            --shadow: 0 18px 40px rgba(10, 42, 58, 0.1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--text);
            background: radial-gradient(circle at 10% 10%, rgba(11, 122, 117, 0.18), transparent 45%),
                        linear-gradient(140deg, #f6fafc, var(--bg));
            min-height: 100vh;
            min-height: 100dvh;
            padding: 24px;
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
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

        .header {
            padding: 28px 30px 20px;
            background: linear-gradient(145deg, #0f3e58, #0b7a75);
            color: #f0f9fa;
        }

        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .header h1 {
            margin: 0;
            font-size: 30px;
            letter-spacing: -0.01em;
        }

        .header p {
            margin: 8px 0 0;
            color: rgba(240, 249, 250, 0.88);
            font-size: 15px;
        }

        .nav {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-link {
            color: #ffffff;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 700;
            transition: background 160ms ease, transform 160ms ease;
            background: rgba(13, 57, 81, 0.22);
        }

        .nav-link:hover,
        .nav-link.is-active {
            background: rgba(255, 255, 255, 0.16);
            transform: translateY(-1px);
        }

        .content {
            padding: 30px;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 18px;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            padding: 24px 30px 24px;
        }

        .card {
            background: #fbfdff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px;
        }

        .metric-card {
            background: linear-gradient(180deg, #ffffff, #f7fbfd);
        }

        .metric-label {
            margin: 0 0 10px;
            color: var(--muted);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
        }

        .metric-value {
            margin: 0;
            font-size: 40px;
            line-height: 1;
            color: var(--accent-strong);
            font-weight: 800;
        }

        .metric-note {
            margin: 10px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .card h2 {
            margin: 0 0 12px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--accent-strong);
        }

        .card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .manage-link {
            display: inline-block;
            margin-top: 14px;
            margin-right: 8px;
            padding: 10px 14px;
            border-radius: 10px;
            text-decoration: none;
            background: linear-gradient(145deg, var(--accent), #0b5d88);
            color: #ffffff;
            font-weight: 700;
        }

        .manage-link:hover {
            background: linear-gradient(145deg, #096662, #084e74);
        }

        .identity strong {
            color: var(--text);
        }

        @media (max-width: 780px) {
            body {
                padding: 12px;
            }

            .shell {
                border-radius: 16px;
            }

            .nav {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }

            .nav-link {
                text-align: center;
            }

            .metrics {
                grid-template-columns: 1fr;
                padding: 22px 22px 0;
            }

            .content {
                grid-template-columns: 1fr;
                padding: 22px;
            }

            .header {
                padding: 22px;
            }

            .header h1 {
                font-size: 24px;
            }

            .metric-value {
                font-size: 34px;
            }
        }

        @media (max-width: 520px) {
            .header {
                padding: 18px;
            }

            .header p {
                font-size: 14px;
            }

            .nav {
                grid-template-columns: 1fr;
            }

            .metrics {
                padding: 18px 18px 0;
            }
        }
    </style>
</head>
<body>
    <main class="shell" role="main">
        <header class="header">
            <div class="header-top">
                <div>
                    <h1>Admin Dashboard</h1>
                    <p>Overview of platform activity, team access, and commerce operations.</p>
                </div>
            </div>

            <nav class="nav" aria-label="Admin navigation">
                <a class="nav-link is-active" href="<?php echo site_url('admin/dashboard'); ?>">Dashboard</a>
                <a class="nav-link" href="<?php echo site_url('admin/users'); ?>">Users</a>
                <a class="nav-link" href="<?php echo site_url('admin/products'); ?>">Products</a>
                <a class="nav-link" href="<?php echo site_url('admin/logout'); ?>">Sign Out</a>
            </nav>
        </header>

        <section class="metrics">
            <article class="card metric-card">
                <p class="metric-label">Total Users</p>
                <p class="metric-value"><?php echo (int) $total_users; ?></p>
                <p class="metric-note">Registered accounts across the platform.</p>
            </article>

            <article class="card metric-card">
                <p class="metric-label">Total Products</p>
                <p class="metric-value"><?php echo (int) $total_products; ?></p>
                <p class="metric-note">Products available in the catalog.</p>
            </article>

            <article class="card metric-card">
                <p class="metric-label">Total Orders</p>
                <p class="metric-value"><?php echo (int) $total_orders; ?></p>
                <p class="metric-note">Orders captured in the system.</p>
            </article>
        </section>

    </main>
</body>
</html>
