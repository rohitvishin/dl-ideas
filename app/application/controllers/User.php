<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->config('stripe', TRUE);
        $this->load->model('Admin_product_model');
    }

    public function index()
    {
        $products = $this->Admin_product_model->getCatalogProducts();
        $isAdminLoggedIn = (bool) $this->session->userdata('admin_logged_in');
        $isUserLoggedIn = (bool) $this->session->userdata('user_logged_in');
        $isLoggedIn = $isAdminLoggedIn || $isUserLoggedIn;
        $accountName = $this->session->userdata('user_name');
        $accountEmail = $this->session->userdata('user_email');

        if ($accountName === '') {
            $accountName = 'User';
        }

        $data = array(
            'title' => 'Product Catalog',
            'products' => $products,
            'product_count' => count($products),
            'purchase_notice' => (string) $this->session->flashdata('purchase_notice'),
            'is_logged_in' => $isLoggedIn,
            'account_name' => $accountName,
            'account_email' => $accountEmail,
        );

        $this->load->view('user/product_list', $data);
    }

    public function buy($slug = '')
    {
        $slug = strtolower(trim((string) $slug));

        $isAdminLoggedIn = (bool) $this->session->userdata('admin_logged_in');
        $isUserLoggedIn = (bool) $this->session->userdata('user_logged_in');
        $isLoggedIn = $isAdminLoggedIn || $isUserLoggedIn;

        if ($isAdminLoggedIn && !$isUserLoggedIn) {
            $this->session->set_flashdata('purchase_notice', 'Checkout is only available for customer accounts. Please log in as a user to continue.');
            redirect('user');
            return;
        }

        $accountName = $isAdminLoggedIn
            ? (string) $this->session->userdata('admin_name')
            : (string) $this->session->userdata('user_name');
        $accountEmail = $isAdminLoggedIn
            ? (string) $this->session->userdata('admin_email')
            : (string) $this->session->userdata('user_email');
        $accountRole = $isAdminLoggedIn ? 'admin' : 'user';

        if ($accountName === '') {
            $accountName = 'User';
        }

        if ($slug === '') {
            $this->session->set_flashdata('purchase_notice', 'Select a product before continuing to checkout.');
            redirect('user');
            return;
        }

        $product = $this->Admin_product_model->findActiveProductBySlug($slug);

        if (!$product) {
            show_404();
            return;
        }

        if ((int) $product['stock'] < 1) {
            $this->session->set_flashdata('purchase_notice', 'This product is currently unavailable.');
            redirect('user');
            return;
        }

        $data = array(
            'title'        => 'Checkout',
            'product'      => $product,
            'purchase_notice' => (string) $this->session->flashdata('purchase_notice'),
            'is_logged_in' => $isLoggedIn,
            'is_user_logged_in' => $isUserLoggedIn,
            'account_name' => $accountName,
            'account_email' => $accountEmail,
            'account_role' => $accountRole,
        );

        $this->load->view('user/checkout', $data);
    }

    public function logout()
    {
        $this->session->unset_userdata(array(
            'user_logged_in',
            'user_id',
            'user_name',
            'user_email',
            'admin_logged_in',
            'admin_id',
            'admin_name',
            'admin_email'
        ));

        $this->session->sess_regenerate(TRUE);
        $this->session->set_flashdata('auth_success', 'You have been logged out successfully.');

        redirect('login');
    }

    public function placeOrder($slug = '')
    {
        if ($this->input->method(TRUE) !== 'POST') {
            redirect('user');
            return;
        }

        $isAdminLoggedIn = (bool) $this->session->userdata('admin_logged_in');
        $isUserLoggedIn  = (bool) $this->session->userdata('user_logged_in');

        if (!$isUserLoggedIn) {
            if ($isAdminLoggedIn) {
                $this->session->set_flashdata('purchase_notice', 'Checkout is only available for customer accounts. Please log in as a user to continue.');
                redirect('user');
                return;
            }

            $this->session->set_flashdata('auth_error', 'Please log in to continue checkout.');
            redirect('login');
            return;
        }

        $slug = strtolower(trim((string) $slug));
        if ($slug === '') {
            redirect('user');
            return;
        }

        $product = $this->Admin_product_model->findActiveProductBySlug($slug);
        if (!$product) {
            show_404();
            return;
        }

        $stock = (int) $product['stock'];
        if ($stock < 1) {
            $this->session->set_flashdata('purchase_notice', 'This product is currently unavailable.');
            redirect('user');
            return;
        }

        $requestedQuantity = (int) $this->input->post('quantity', TRUE);
        $quantity = $requestedQuantity > 0 ? $requestedQuantity : 1;
        if ($quantity > $stock) {
            $quantity = $stock;
        }

        $firstName = trim((string) $this->input->post('first_name', TRUE));
        $lastName = trim((string) $this->input->post('last_name', TRUE));
        $email = trim((string) $this->input->post('email', TRUE));
        $phone = trim((string) $this->input->post('phone', TRUE));
        $addressLine1 = trim((string) $this->input->post('address_line1', TRUE));
        $addressLine2 = trim((string) $this->input->post('address_line2', TRUE));
        $city = trim((string) $this->input->post('city', TRUE));
        $state = trim((string) $this->input->post('state', TRUE));
        $postalCode = trim((string) $this->input->post('postal_code', TRUE));
        $country = strtoupper(trim((string) $this->input->post('country', TRUE)));

        if ($firstName === '' || $lastName === '' || $email === '' || $addressLine1 === '' || $city === '' || $state === '' || $postalCode === '' || $country === '') {
            $this->session->set_flashdata('purchase_notice', 'Please complete all required customer and shipping fields.');
            redirect('buy-now/' . $slug);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('purchase_notice', 'Please enter a valid email address.');
            redirect('buy-now/' . $slug);
            return;
        }

        if (!preg_match('/^[A-Z]{2}$/', $country)) {
            $this->session->set_flashdata('purchase_notice', 'Country must be a valid 2-letter ISO code (for example: US, IN).');
            redirect('buy-now/' . $slug);
            return;
        }

        $unitAmount = (int) round(((float) $product['price']) * 100);
        if ($unitAmount < 50) {
            $this->session->set_flashdata('purchase_notice', 'Product price is invalid for payment processing.');
            redirect('buy-now/' . $slug);
            return;
        }

        $secretKey = (string) $this->config->item('stripe_secret_key', 'stripe');
        $currency  = (string) $this->config->item('stripe_currency', 'stripe');

        if ($secretKey === '') {
            $this->session->set_flashdata('purchase_notice', 'Stripe is not configured. Add STRIPE_SECRET_KEY to continue.');
            redirect('buy-now/' . $slug);
            return;
        }

        if (!function_exists('curl_init')) {
            $this->session->set_flashdata('purchase_notice', 'Payment service is unavailable on this server (cURL missing).');
            redirect('buy-now/' . $slug);
            return;
        }

        $currencyCode = $currency !== '' ? strtolower($currency) : 'usd';
        $successUrl   = site_url('checkout/success') . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl    = site_url('checkout/failure') . '?product=' . $slug;

        $productData = array(
            'name' => (string) $product['name'],
        );
        $productDescription = trim((string) $product['description']);
        if ($productDescription !== '') {
            $productData['description'] = $productDescription;
        }

        $shippingAddress = array(
            'line1'       => $addressLine1,
            'city'        => $city,
            'state'       => $state,
            'postal_code' => $postalCode,
            'country'     => $country,
        );
        if ($addressLine2 !== '') {
            $shippingAddress['line2'] = $addressLine2;
        }

        $shippingDetails = array(
            'name'    => trim($firstName . ' ' . $lastName),
            'address' => $shippingAddress,
        );
        if ($phone !== '') {
            $shippingDetails['phone'] = $phone;
        }

        $payload = http_build_query(array(
            'mode'        => 'payment',
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'customer_email' => $email,
            'payment_intent_data' => array(
                'shipping' => $shippingDetails,
            ),
            'metadata' => array(
                'customer_first_name' => $firstName,
                'customer_last_name'  => $lastName,
                'customer_email'      => $email,
                'shipping_city'       => $city,
                'shipping_state'      => $state,
                'shipping_postal_code'=> $postalCode,
                'shipping_country'    => $country,
            ),
            'line_items'  => array(
                array(
                    'quantity'   => $quantity,
                    'price_data' => array(
                        'currency'     => $currencyCode,
                        'unit_amount'  => $unitAmount,
                        'product_data' => $productData,
                    ),
                ),
            ),
        ));

        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt($ch, CURLOPT_POST,           TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
            'Authorization: Bearer ' . $secretKey,
            'Content-Type: application/x-www-form-urlencoded',
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT,        30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $responseBody = curl_exec($ch);
        $statusCode   = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError    = curl_error($ch);
        curl_close($ch);

        // In local/dev containers, CA bundles can be missing. Retry once without SSL verification.
        $canRetryWithoutVerify = defined('ENVIRONMENT') && ENVIRONMENT !== 'production'
            && $responseBody === FALSE
            && stripos($curlError, 'SSL certificate') !== FALSE;

        if ($canRetryWithoutVerify) {
            $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
            curl_setopt($ch, CURLOPT_POST,           TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS,     $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
                'Authorization: Bearer ' . $secretKey,
                'Content-Type: application/x-www-form-urlencoded',
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT,        30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            $responseBody = curl_exec($ch);
            $statusCode   = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError    = curl_error($ch);
            curl_close($ch);
        }

        if ($responseBody === FALSE || $statusCode >= 400) {
            $stripeErrorMessage = '';

            if (is_string($responseBody) && $responseBody !== '') {
                $errorBody = json_decode($responseBody, TRUE);
                if (is_array($errorBody) && isset($errorBody['error']['message'])) {
                    $stripeErrorMessage = (string) $errorBody['error']['message'];
                }
            }

            $finalError = 'Unable to start payment right now.';
            if ($stripeErrorMessage !== '') {
                $finalError = 'Unable to start payment: ' . $stripeErrorMessage;
            } elseif ($curlError !== '') {
                $finalError = 'Unable to start payment: ' . $curlError;
            }

            $this->session->set_flashdata('purchase_notice', $finalError);
            redirect('buy-now/' . $slug);
            return;
        }

        $response = json_decode($responseBody, TRUE);
        if (!is_array($response) || empty($response['url'])) {
            $this->session->set_flashdata('purchase_notice', 'Payment gateway response was invalid. Please try again.');
            redirect('buy-now/' . $slug);
            return;
        }

        redirect($response['url']);
    }

    public function paymentSuccess()
    {
        $sessionId = (string) $this->input->get('session_id', TRUE);

        $data = array(
            'title'      => 'Payment Successful',
            'session_id' => $sessionId,
        );

        $this->load->view('user/payment_success', $data);
    }

    public function paymentFailure()
    {
        $productSlug = (string) $this->input->get('product', TRUE);

        $data = array(
            'title' => 'Payment Failed',
            'product_slug' => $productSlug
        );

        $this->load->view('user/payment_failure', $data);
    }
}