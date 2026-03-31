<?php defined('BASEPATH') OR exit('No direct script access allowed');

$session      = isset($this->session) ? $this->session : NULL;
$is_logged_in = $session ? (bool) $session->userdata('user_logged_in') : FALSE;
$user_name    = $is_logged_in ? trim((string) $session->userdata('user_name')) : '';
$account_label = ($user_name !== '') ? $user_name : 'Account';
?>

<link rel="stylesheet" href="<?php echo base_url('assets/css/store_nav.css'); ?>">

<header class="nav" role="banner">
	<a class="logo-link" href="<?php echo $is_logged_in ? site_url('user') : site_url('login'); ?>" aria-label="Ecom Nova home">
		<span class="logo-mark" aria-hidden="true">EC</span>
		<span class="logo-copy">
			<span class="logo-title">Ecom Nova</span>
			<span class="logo-tag">Online Store</span>
		</span>
	</a>

	<div class="nav-right">
		
		<details class="dropdown">
			<summary class="nav-chip dropdown-summary">
				<?php echo $is_logged_in ? html_escape($account_label) : 'Login'; ?>
			</summary>
			<div class="dropdown-menu">
				<?php if ($is_logged_in): ?>
					<a class="dropdown-link" href="<?php echo site_url('my-orders'); ?>">My Orders</a>
					<a class="dropdown-link logout" href="<?php echo site_url('logout'); ?>">Logout</a>
				<?php else: ?>
					<a class="dropdown-link" href="<?php echo site_url('login'); ?>">Sign In</a>
				<?php endif; ?>
			</div>
		</details>
	</div>
</header>