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
			--bg: #eff4f1;
			--panel: #ffffff;
			--text: #152c34;
			--muted: #637a83;
			--accent: #0b766f;
			--accent-strong: #095752;
			--warm: #f2ba67;
			--border: rgba(21, 44, 52, 0.12);
			--shadow: 0 22px 50px rgba(16, 39, 48, 0.12);
			--card-shadow: 0 14px 28px rgba(12, 38, 43, 0.08);
		}

		* {
			box-sizing: border-box;
		}

		body {
			margin: 0;
			min-height: 100vh;
			min-height: 100dvh;
			font-family: 'Manrope', sans-serif;
			color: var(--text);
			background:
				radial-gradient(circle at 0% 0%, rgba(11, 118, 111, 0.2), transparent 35%),
				radial-gradient(circle at 100% 10%, rgba(242, 186, 103, 0.25), transparent 35%),
				linear-gradient(160deg, #f7faf8 0%, var(--bg) 62%, #edf3ef 100%);
			padding: 24px;
		}

		.shell {
			max-width: 1240px;
			margin: 0 auto;
			background: var(--panel);
			border: 1px solid rgba(255, 255, 255, 0.75);
			border-radius: 24px;
			overflow: hidden;
			box-shadow: var(--shadow);
		}

		.nav {
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 16px;
			padding: 16px 24px;
			color: #eef9f8;
			background: linear-gradient(140deg, #0c3f5a 0%, #0b766f 100%);
			border-bottom: 1px solid rgba(255, 255, 255, 0.2);
		}

		.logo-link {
			text-decoration: none;
			color: inherit;
			display: inline-flex;
			align-items: center;
			gap: 12px;
		}

		.logo-mark {
			width: 40px;
			height: 40px;
			border-radius: 12px;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			font-size: 14px;
			font-weight: 800;
			letter-spacing: 0.03em;
			background: linear-gradient(140deg, #f6c579, #ffebca);
			color: #1d3e49;
			box-shadow: 0 8px 18px rgba(0, 0, 0, 0.22);
		}

		.logo-copy {
			display: grid;
			gap: 2px;
		}

		.logo-title {
			font-size: 16px;
			line-height: 1;
			font-weight: 800;
			letter-spacing: -0.01em;
		}

		.logo-tag {
			font-size: 11px;
			text-transform: uppercase;
			letter-spacing: 0.09em;
			color: rgba(238, 249, 248, 0.78);
			font-weight: 700;
		}

		.nav-right {
			display: flex;
			align-items: center;
			gap: 10px;
			flex-wrap: wrap;
		}

		.nav-chip {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			text-decoration: none;
			color: #f5fcfb;
			font-size: 13px;
			font-weight: 700;
			border-radius: 999px;
			padding: 10px 14px;
			border: 1px solid rgba(255, 255, 255, 0.26);
			background: rgba(255, 255, 255, 0.11);
			transition: transform 160ms ease, background 160ms ease;
		}

		.nav-chip:hover {
			transform: translateY(-1px);
			background: rgba(255, 255, 255, 0.2);
		}

		.dropdown {
			position: relative;
		}

		.dropdown-summary {
			list-style: none;
			cursor: pointer;
			user-select: none;
		}

		.dropdown-summary::-webkit-details-marker {
			display: none;
		}

		.dropdown-menu {
			position: absolute;
			right: 0;
			top: calc(100% + 10px);
			min-width: 190px;
			display: grid;
			gap: 8px;
			padding: 10px;
			border-radius: 14px;
			border: 1px solid rgba(16, 43, 52, 0.16);
			background: #ffffff;
			box-shadow: 0 18px 34px rgba(10, 37, 43, 0.2);
			z-index: 20;
		}

		.dropdown-link {
			text-decoration: none;
			color: var(--text);
			font-size: 13px;
			font-weight: 700;
			padding: 10px 12px;
			border-radius: 10px;
			border: 1px solid rgba(21, 44, 52, 0.1);
			background: #f8fbfa;
		}

		.dropdown-link:hover {
			background: #edf5f3;
			color: var(--accent-strong);
		}

		.dropdown-link.logout {
			color: #8f312f;
			background: #fff4f3;
			border-color: rgba(166, 71, 61, 0.22);
		}

		.hero {
			padding: 28px 28px 16px;
		}

		.hero h1 {
			margin: 0;
			font-size: clamp(28px, 4vw, 42px);
			letter-spacing: -0.03em;
			line-height: 1;
		}

		.hero p {
			margin: 10px 0 0;
			color: var(--muted);
			max-width: 72ch;
			line-height: 1.7;
		}

		.catalog-wrap {
			padding: 16px 28px 30px;
		}

		.top-bar {
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 14px;
			margin-bottom: 18px;
			padding: 12px 14px;
			border: 1px solid var(--border);
			border-radius: 14px;
			background: #f8fbf9;
		}

		.top-bar .count {
			font-size: 13px;
			text-transform: uppercase;
			letter-spacing: 0.08em;
			color: var(--muted);
			font-weight: 700;
		}

		.top-bar .status {
			font-size: 13px;
			font-weight: 700;
			color: var(--accent-strong);
		}

		.notice {
			margin: 0 0 16px;
			padding: 14px 16px;
			border-radius: 12px;
			border: 1px solid rgba(11, 118, 111, 0.22);
			background: linear-gradient(145deg, rgba(11, 118, 111, 0.12), rgba(242, 186, 103, 0.18));
			color: var(--accent-strong);
			font-weight: 700;
			font-size: 14px;
		}

		.grid {
			display: grid;
			grid-template-columns: repeat(3, minmax(0, 1fr));
			gap: 18px;
		}

		.card {
			border: 1px solid var(--border);
			border-radius: 18px;
			overflow: hidden;
			background: #ffffff;
			box-shadow: var(--card-shadow);
			display: grid;
			grid-template-rows: auto 1fr auto;
			transition: transform 160ms ease, box-shadow 160ms ease;
		}

		.card:hover {
			transform: translateY(-3px);
			box-shadow: 0 20px 34px rgba(11, 37, 42, 0.13);
		}

		.thumb {
			position: relative;
			height: 210px;
			background: linear-gradient(145deg, #e8f2ed, #d8e8e1);
		}

		.thumb img {
			width: 100%;
			height: 100%;
			object-fit: cover;
			display: block;
		}

		.badge {
			position: absolute;
			top: 10px;
			left: 10px;
			border-radius: 999px;
			padding: 6px 10px;
			font-size: 11px;
			font-weight: 800;
			letter-spacing: 0.08em;
			text-transform: uppercase;
			color: #0d4f48;
			background: rgba(255, 255, 255, 0.94);
			border: 1px solid rgba(21, 44, 52, 0.12);
		}

		.stock-badge {
			position: absolute;
			top: 10px;
			right: 10px;
			border-radius: 999px;
			padding: 6px 10px;
			font-size: 11px;
			font-weight: 800;
			letter-spacing: 0.06em;
			text-transform: uppercase;
			color: #14453f;
			background: rgba(238, 253, 247, 0.94);
			border: 1px solid rgba(20, 69, 63, 0.18);
		}

		.stock-badge.is-empty {
			color: #8b3e2f;
			background: rgba(255, 243, 240, 0.95);
			border-color: rgba(150, 66, 53, 0.2);
		}

		.card-body {
			padding: 14px;
		}

		.product-name {
			margin: 0;
			font-size: 19px;
			letter-spacing: -0.02em;
			line-height: 1.2;
		}

		.desc {
			margin: 8px 0 0;
			color: var(--muted);
			font-size: 14px;
			line-height: 1.65;
			min-height: 68px;
		}

		.price-row {
			margin-top: 12px;
			display: flex;
			align-items: baseline;
			gap: 8px;
		}

		.price {
			font-size: 28px;
			font-weight: 800;
			letter-spacing: -0.03em;
			color: var(--accent-strong);
			line-height: 1;
		}

		.price-note {
			color: var(--muted);
			font-size: 12px;
			text-transform: uppercase;
			letter-spacing: 0.07em;
			font-weight: 700;
		}

		.card-foot {
			display: flex;
			align-items: center;
			justify-content: flex-end;
			gap: 10px;
			padding: 0 14px 14px;
		}

		.buy {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			border-radius: 999px;
			min-width: 132px;
			padding: 12px 16px;
			text-decoration: none;
			font-size: 13px;
			text-transform: uppercase;
			letter-spacing: 0.05em;
			font-weight: 800;
			color: #ffffff;
			background: linear-gradient(145deg, var(--accent), #168568);
			box-shadow: 0 12px 20px rgba(11, 118, 111, 0.24);
		}

		.buy:hover {
			transform: translateY(-1px);
		}

		.buy.is-disabled {
			background: #d8deda;
			color: #687a72;
			box-shadow: none;
			pointer-events: none;
		}

		.empty {
			border: 1px dashed rgba(21, 44, 52, 0.22);
			border-radius: 14px;
			padding: 30px;
			text-align: center;
			color: var(--muted);
			background: rgba(255, 255, 255, 0.7);
			line-height: 1.8;
		}

		@media (max-width: 1080px) {
			.grid {
				grid-template-columns: repeat(2, minmax(0, 1fr));
			}
		}

		@media (max-width: 760px) {
			body {
				padding: 12px;
			}

			.shell {
				border-radius: 16px;
			}

			.nav {
				padding: 14px;
				flex-direction: column;
				align-items: stretch;
			}

			.nav-right {
				width: 100%;
				display: grid;
				grid-template-columns: 1fr;
			}

			.nav-chip {
				justify-content: center;
			}

			.dropdown-menu {
				left: 0;
				right: 0;
			}

			.hero {
				padding: 20px 16px 12px;
			}

			.catalog-wrap {
				padding: 10px 16px 18px;
			}

			.grid {
				grid-template-columns: 1fr;
			}

			.thumb {
				height: 220px;
			}

			.top-bar {
				flex-direction: column;
				align-items: flex-start;
			}
		}

		@media (prefers-reduced-motion: reduce) {
			*, *::before, *::after {
				transition: none !important;
				animation: none !important;
			}
		}
	</style>
</head>
<body>
	<main class="shell" role="main">
		<header class="nav" role="banner">
			<a class="logo-link" href="<?php echo site_url('user'); ?>" aria-label="Ecommerce home">
				<span class="logo-mark" aria-hidden="true">EC</span>
				<span class="logo-copy">
					<span class="logo-title">Ecom Nova</span>
					<span class="logo-tag">Online Store</span>
				</span>
			</a>

			<div class="nav-right">
				<a class="nav-chip" href="mailto:support@projecthub.local">Support: support@projecthub.local</a>

				<details class="dropdown">
					<summary class="nav-chip dropdown-summary">
						<?php if ($is_logged_in): ?>
							<?php echo html_escape($account_name); ?>
						<?php else: ?>
							Login
						<?php endif; ?>
					</summary>
					<div class="dropdown-menu">
						<?php if ($is_logged_in): ?>
							<a class="dropdown-link" href="<?php echo ($account_role === 'admin') ? site_url('admin/dashboard') : site_url('user'); ?>">My Account</a>
							<a class="dropdown-link logout" href="<?php echo site_url('logout'); ?>">Logout</a>
						<?php else: ?>
							<a class="dropdown-link" href="<?php echo site_url('login'); ?>">Sign In</a>
						<?php endif; ?>
					</div>
				</details>
			</div>
		</header>

		<section class="hero">
			<h1>Shop Trending Products</h1>
			<p>Explore our curated collection with transparent pricing, live availability, and instant checkout-ready actions.</p>
		</section>

		<section class="catalog-wrap">
			<?php if ($purchase_notice !== ''): ?>
				<div class="notice"><?php echo html_escape($purchase_notice); ?></div>
			<?php endif; ?>

			<div class="top-bar">
				<span class="count"><?php echo (int) $product_count; ?> Products Available</span>
				<span class="status">Fast shipping and secure checkout</span>
			</div>

			<?php if (!empty($products)): ?>
				<div class="grid">
					<?php foreach ($products as $product): ?>
						<?php
							$name = (string) $product['name'];
							$description = trim((string) $product['description']) !== ''
								? (string) $product['description']
								: 'Premium quality product crafted for reliability and everyday convenience.';
							$stock = (int) $product['stock'];
							$isAvailable = $stock > 0;
							$image = trim((string) $product['image']);
							$imageUrl = $image !== '' ? base_url($image) : base_url('app/uploads/products/placeholder.svg');
						?>
						<article class="card">
							<div class="thumb">
								<?php if ($imageUrl !== ''): ?>
									<img src="<?php echo html_escape($imageUrl); ?>" alt="<?php echo html_escape($name); ?>">
								<?php endif; ?>
								<span class="badge"><?php echo $isAvailable ? 'In Stock' : 'Sold Out'; ?></span>
								<span class="stock-badge<?php echo $isAvailable ? '' : ' is-empty'; ?>">Qty: <?php echo $stock; ?></span>
							</div>

							<div class="card-body">
								<h2 class="product-name"><?php echo html_escape($name); ?></h2>
								<p class="desc"><?php echo html_escape($description); ?></p>

								<div class="price-row">
									<span class="price">$<?php echo number_format((float) $product['price'], 2); ?></span>
									<span class="price-note">per item</span>
								</div>
							</div>

							<div class="card-foot">
								<a class="buy<?php echo $isAvailable ? '' : ' is-disabled'; ?>" href="<?php echo $isAvailable ? site_url('buy-now/'.$product['slug']) : '#'; ?>">Buy Now</a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<div class="empty">No products are available right now. Please check back soon.</div>
			<?php endif; ?>
		</section>
	</main>
</body>
</html>
