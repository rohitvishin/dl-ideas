<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->library(array('session', 'form_validation'));
		$this->load->helper(array('url', 'form'));
		$this->load->model('Admin_user_model');
		$this->load->model('Admin_product_model');
		$this->load->model('Admin_order_model');
	}

	public function index()
	{
		return $this->login();
	}

	public function login()
	{
		if ($this->isAuthenticated()) {
			redirect('admin/dashboard');
			return;
		}

		if ($this->isUserAuthenticated()) {
			redirect('user');
			return;
		}

		$data = array(
			'title' => 'Login'
		);

		$this->load->view('login', $data);
	}

	public function authenticate()
	{
		if ($this->input->method(TRUE) !== 'POST') {
			redirect('login');
			return;
		}

		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required|max_length[255]');

		if ($this->form_validation->run() === FALSE) {
			$this->load->view('login', array('title' => 'Login'));
			return;
		}

		$email = strtolower(trim((string) $this->input->post('email', TRUE)));
		$password = (string) $this->input->post('password', FALSE);

		$account = $this->Admin_user_model->findActiveUserByEmail($email);

		if (!$account || !$this->verifyPassword($password, $account['password'])) {
			$this->session->set_flashdata('auth_error', 'Invalid email or password.');
			redirect('login');
			return;
		}

		// Upgrade plain-text legacy passwords to a strong hash after successful login.
		if ($this->isLegacyPassword($account['password'])) {
			$this->Admin_user_model->updatePasswordHash((int) $account['id'], password_hash($password, PASSWORD_DEFAULT));
		}

		$this->session->sess_regenerate(TRUE);

		if ((string) $account['role'] === 'admin') {
			$this->session->unset_userdata(array('user_logged_in', 'user_id', 'user_name', 'user_email'));
			$this->session->set_userdata(array(
				'admin_logged_in' => TRUE,
				'admin_id' => (int) $account['id'],
				'admin_name' => $account['name'],
				'admin_email' => $account['email']
			));

			redirect('admin/dashboard');
			return;
		}

		$this->session->unset_userdata(array('admin_logged_in', 'admin_id', 'admin_name', 'admin_email'));
		$this->session->set_userdata(array(
			'user_logged_in' => TRUE,
			'user_id' => (int) $account['id'],
			'user_name' => $account['name'],
			'user_email' => $account['email']
		));

		redirect('user');
	}

	public function dashboard()
	{
		$this->requireAuth();

		$data = array(
			'title' => 'Admin Dashboard',
			'admin_name' => (string) $this->session->userdata('admin_name'),
			'admin_email' => (string) $this->session->userdata('admin_email'),
			'total_users' => $this->Admin_user_model->countAllUsers(),
			'total_products' => $this->Admin_product_model->countAllProducts(),
			'total_orders' => $this->Admin_order_model->countAllOrders()
		);

		$this->load->view('admin/dashboard', $data);
	}

	public function users()
	{
		$this->requireAuth();

		$data = array(
			'title' => 'Create Users',
			'admin_name' => (string) $this->session->userdata('admin_name'),
			'admin_email' => (string) $this->session->userdata('admin_email'),
			'recent_users' => $this->Admin_user_model->getRecentUsers(12),
			'form_error' => (string) $this->session->flashdata('user_form_error'),
			'form_success' => (string) $this->session->flashdata('user_form_success'),
			'old_input' => (array) $this->session->flashdata('user_old_input')
		);

		$this->load->view('admin/users_create', $data);
	}

	public function createUser()
	{
		$this->requireAuth();

		if ($this->input->method(TRUE) !== 'POST') {
			redirect('admin/users');
			return;
		}

		$this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|max_length[100]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[150]|is_unique[users.email]');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[255]');
		$this->form_validation->set_rules('role', 'Role', 'required|in_list[user,admin]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|max_length[20]');
		$this->form_validation->set_rules('status', 'Status', 'required|in_list[0,1]');

		$oldInput = array(
			'name' => (string) $this->input->post('name', TRUE),
			'email' => strtolower(trim((string) $this->input->post('email', TRUE))),
			'role' => (string) $this->input->post('role', TRUE),
			'phone' => (string) $this->input->post('phone', TRUE),
			'status' => (string) $this->input->post('status', TRUE)
		);

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('user_form_error', validation_errors());
			$this->session->set_flashdata('user_old_input', $oldInput);
			redirect('admin/users');
			return;
		}

		$userData = array(
			'name' => trim($oldInput['name']),
			'email' => $oldInput['email'],
			'password' => password_hash((string) $this->input->post('password', FALSE), PASSWORD_DEFAULT),
			'role' => $oldInput['role'],
			'phone' => trim($oldInput['phone']) !== '' ? trim($oldInput['phone']) : NULL,
			'status' => (int) $oldInput['status'],
			'created_at' => date('Y-m-d H:i:s')
		);

		$created = $this->Admin_user_model->createUser($userData);

		if (!$created) {
			$this->session->set_flashdata('user_form_error', 'Unable to create user at the moment. Please try again.');
			$this->session->set_flashdata('user_old_input', $oldInput);
			redirect('admin/users');
			return;
		}

		$this->session->set_flashdata('user_form_success', 'User account created successfully.');
		redirect('admin/users');
	}

	public function products()
	{
		$this->requireAuth();

		$data = array(
			'title' => 'Add Products',
			'admin_name' => (string) $this->session->userdata('admin_name'),
			'admin_email' => (string) $this->session->userdata('admin_email'),
			'recent_products' => $this->Admin_product_model->getRecentProducts(12),
			'form_error' => (string) $this->session->flashdata('product_form_error'),
			'form_success' => (string) $this->session->flashdata('product_form_success'),
			'old_input' => (array) $this->session->flashdata('product_old_input')
		);

		$this->load->view('admin/products_create', $data);
	}

	public function orders()
	{
		$this->requireAuth();

		$data = array(
			'title' => 'Order List',
			'admin_name' => (string) $this->session->userdata('admin_name'),
			'admin_email' => (string) $this->session->userdata('admin_email'),
			'orders' => $this->Admin_order_model->getRecentOrdersForAdmin(200)
		);

		$this->load->view('admin/orders_list', $data);
	}

	public function invoice($orderId = 0)
	{
		$this->requireAuth();

		$orderId = (int) $orderId;
		if ($orderId < 1) {
			show_404();
			return;
		}

		$order = $this->Admin_order_model->getOrderDetailsForAdmin($orderId);
		if (!$order) {
			show_404();
			return;
		}

		$data = array(
			'title' => 'Invoice #' . (int) $order['id'],
			'order' => $order,
			'viewer_role' => 'admin',
			'viewer_name' => (string) $this->session->userdata('admin_name')
		);

		$this->load->view('invoice/order_invoice', $data);
	}

	public function receipt($orderId = 0)
	{
		$this->requireAuth();

		$orderId = (int) $orderId;
		if ($orderId < 1) {
			show_404();
			return;
		}

		$order = $this->Admin_order_model->getOrderDetailsForAdmin($orderId);
		if (!$order) {
			show_404();
			return;
		}

		$data = array(
			'title' => 'Receipt #' . (int) $order['id'],
			'order' => $order,
			'viewer_role' => 'admin',
			'viewer_name' => (string) $this->session->userdata('admin_name')
		);

		$this->load->view('invoice/order_receipt', $data);
	}

	public function createProduct()
	{
		$this->requireAuth();

		if ($this->input->method(TRUE) !== 'POST') {
			redirect('admin/products');
			return;
		}

		$this->form_validation->set_rules('name', 'Product Name', 'trim|required|min_length[2]|max_length[150]');
		$this->form_validation->set_rules('slug', 'Slug', 'trim|max_length[150]|alpha_dash');
		$this->form_validation->set_rules('description', 'Description', 'trim');
		$this->form_validation->set_rules('price', 'Price', 'required|numeric|greater_than[0]');
		$this->form_validation->set_rules('stock', 'Stock', 'required|integer|greater_than_equal_to[0]');
		$this->form_validation->set_rules('status', 'Status', 'required|in_list[0,1]');

		$oldInput = array(
			'name' => (string) $this->input->post('name', TRUE),
			'slug' => (string) $this->input->post('slug', TRUE),
			'description' => (string) $this->input->post('description', TRUE),
			'price' => (string) $this->input->post('price', TRUE),
			'stock' => (string) $this->input->post('stock', TRUE),
			'status' => (string) $this->input->post('status', TRUE)
		);

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('product_form_error', validation_errors());
			$this->session->set_flashdata('product_old_input', $oldInput);
			redirect('admin/products');
			return;
		}

		$slug = strtolower(trim($oldInput['slug']));
		if ($slug === '') {
			$slug = url_title((string) $oldInput['name'], '-', TRUE);
		}

		if ($slug === '') {
			$this->session->set_flashdata('product_form_error', 'Please provide a valid product name or slug.');
			$this->session->set_flashdata('product_old_input', $oldInput);
			redirect('admin/products');
			return;
		}

		if ($this->Admin_product_model->existsBySlug($slug)) {
			$this->session->set_flashdata('product_form_error', 'Slug already exists. Please choose a unique slug.');
			$this->session->set_flashdata('product_old_input', $oldInput);
			redirect('admin/products');
			return;
		}

		if (!is_dir(PRODUCT_UPLOAD_DIR) && !@mkdir(PRODUCT_UPLOAD_DIR, 0755, TRUE)) {
			$this->session->set_flashdata('product_form_error', 'Unable to prepare product image storage directory.');
			$this->session->set_flashdata('product_old_input', $oldInput);
			redirect('admin/products');
			return;
		}

		$imagePath = PRODUCT_PLACEHOLDER_PATH;

		if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
			$uploadConfig = array(
				'upload_path' => PRODUCT_UPLOAD_DIR,
				'allowed_types' => 'jpg|png',
				'max_size' => 4096,
				'encrypt_name' => TRUE
			);

			$this->load->library('upload', $uploadConfig);

			if (!$this->upload->do_upload('image')) {
				$this->session->set_flashdata('product_form_error', $this->upload->display_errors('', ''));
				$this->session->set_flashdata('product_old_input', $oldInput);
				redirect('admin/products');
				return;
			}

			$uploadData = $this->upload->data();
			$imagePath = PRODUCT_UPLOAD_URI.$uploadData['file_name'];
		}

		$productData = array(
			'name' => trim($oldInput['name']),
			'slug' => $slug,
			'description' => trim($oldInput['description']) !== '' ? trim($oldInput['description']) : NULL,
			'price' => number_format((float) $oldInput['price'], 2, '.', ''),
			'stock' => (int) $oldInput['stock'],
			'image' => $imagePath,
			'status' => (int) $oldInput['status'],
			'created_at' => date('Y-m-d H:i:s')
		);

		$created = $this->Admin_product_model->createProduct($productData);

		if (!$created) {
			$this->session->set_flashdata('product_form_error', 'Unable to create product at the moment. Please try again.');
			$this->session->set_flashdata('product_old_input', $oldInput);
			redirect('admin/products');
			return;
		}

		$this->session->set_flashdata('product_form_success', 'Product created successfully.');
		redirect('admin/products');
	}

	public function logout()
	{
		$this->session->unset_userdata(array(
			'admin_logged_in',
			'admin_id',
			'admin_name',
			'admin_email'
		));

		$this->session->sess_regenerate(TRUE);
		$this->session->set_flashdata('auth_success', 'You have been logged out successfully.');

		redirect('login');
	}

	private function isAuthenticated()
	{
		return (bool) $this->session->userdata('admin_logged_in');
	}

	private function requireAuth()
	{
		if (!$this->isAuthenticated()) {
			$this->session->set_flashdata('auth_error', 'Please sign in to continue.');
			redirect('login');
			exit;
		}
	}

	private function isUserAuthenticated()
	{
		return (bool) $this->session->userdata('user_logged_in');
	}

	private function verifyPassword($plainPassword, $storedPassword)
	{
		if ($this->isLegacyPassword($storedPassword)) {
			return hash_equals((string) $storedPassword, (string) $plainPassword);
		}

		return password_verify((string) $plainPassword, (string) $storedPassword);
	}

	private function isLegacyPassword($storedPassword)
	{
		return password_get_info((string) $storedPassword)['algo'] === 0;
	}
}
