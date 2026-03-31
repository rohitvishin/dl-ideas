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
	<link rel="stylesheet" href="<?php echo base_url('assets/css/user_product_list.css'); ?>">
</head>
<body>
	<main class="shell" role="main">
		<?php $this->load->view('components/store_nav'); ?>

		<section class="hero">
			<h1>Shop Trending Products</h1>			
		</section>

		<section class="catalog-wrap">
			<?php if ($purchase_notice !== ''): ?>
				<div class="notice"><?php echo html_escape($purchase_notice); ?></div>
			<?php endif; ?>

			<div class="top-bar">
				<span class="count"><?php echo (int) $product_count; ?> Products Available</span>
				<span class="status">Fast and secure checkout</span>
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
							$imageUrl = $image !== '' ? base_url($image) : base_url('uploads/products/placeholder.svg');
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
