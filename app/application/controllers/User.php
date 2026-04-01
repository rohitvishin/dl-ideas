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
        $this->load->model('Product_model');
        $this->load->model('Order_model');
        $this->load->model('Logs_model');
    }

    public function index()
    {
        $products = $this->Product_model->getCatalogProducts();
        $isAdminLoggedIn = (bool) $this->session->userdata('admin_logged_in');
        $isUserLoggedIn = (bool) $this->session->userdata('user_logged_in');
        $isLoggedIn = $isAdminLoggedIn || $isUserLoggedIn;
        $account_role = $isAdminLoggedIn ? 'admin' : 'user';
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
            'account_role' => $account_role,
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

        $product = $this->Product_model->findActiveProductBySlug($slug);

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
            'user_email'
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

        $product = $this->Product_model->findActiveProductBySlug($slug);
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
                'user_id'             => (string) $this->session->userdata('user_id'),
                'product_id'          => (string) $product['id'],
                'quantity'            => (string) $quantity,
                'unit_price'          => (string) $product['price'],
                'full_name'           => trim($firstName . ' ' . $lastName),
                'phone'               => $phone,
                'address_line1'       => $addressLine1,
                'address_line2'       => $addressLine2,
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

            // Log failed Stripe checkout attempt
            $this->Logs_model->insert('stripe_checkout_failed', array(
                'product_id'  => (int) $product['id'],
                'slug'        => $slug,
                'http_status' => $statusCode,
                'error'       => $finalError,
            ), $this->session->userdata('user_id'));

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

        // Log successful Stripe checkout session creation
        $this->Logs_model->insert('stripe_checkout_created', array(
            'product_id'        => (int) $product['id'],
            'slug'              => $slug,
            'quantity'          => $quantity,
            'unit_amount'       => $unitAmount,
            'currency'          => $currencyCode,
            'stripe_session_id' => isset($response['id']) ? $response['id'] : '',
        ), $this->session->userdata('user_id'));

        redirect($response['url']);
    }

    public function paymentSuccess()
    {
        $sessionId = (string) $this->input->get('session_id', TRUE);

        // Validate session_id format to prevent SSRF
        if (!preg_match('/^cs_(test|live)_[a-zA-Z0-9]+$/', $sessionId)) {
            redirect('user');
            return;
        }

        $this->load->model('Order_model');

        // Idempotency: if already processed, skip insertion
        $alreadyProcessed = $this->Order_model->orderExistsByStripeSession($sessionId);

        if (!$alreadyProcessed) {
            $secretKey = (string) $this->config->item('stripe_secret_key', 'stripe');
            $sessionEndpoint = 'https://api.stripe.com/v1/checkout/sessions/' . urlencode($sessionId) . '?expand%5B%5D=payment_intent.latest_charge';

            $ch = curl_init($sessionEndpoint);
            curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Authorization: Bearer ' . $secretKey));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT,        30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

            $responseBody = curl_exec($ch);
            $statusCode   = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError    = curl_error($ch);
            curl_close($ch);

            // Retry without SSL verification in non-production (missing CA bundle)
            $canRetryWithoutVerify = defined('ENVIRONMENT') && ENVIRONMENT !== 'production'
                && $responseBody === FALSE
                && stripos($curlError, 'SSL certificate') !== FALSE;

            if ($canRetryWithoutVerify) {
                $ch = curl_init($sessionEndpoint);
                curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Authorization: Bearer ' . $secretKey));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_TIMEOUT,        30);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

                $responseBody = curl_exec($ch);
                $statusCode   = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
            }

            if ($responseBody !== FALSE && $statusCode === 200) {
                $session = json_decode($responseBody, TRUE);

                if (is_array($session) && ($session['payment_status'] ?? '') === 'paid') {
                    $meta       = isset($session['metadata']) && is_array($session['metadata']) ? $session['metadata'] : array();
                    $paymentIntentId = '';
                    $chargeId = '';
                    if (isset($session['payment_intent'])) {
                        if (is_array($session['payment_intent'])) {
                            $paymentIntentId = trim((string) ($session['payment_intent']['id'] ?? ''));
                            if (isset($session['payment_intent']['latest_charge'])) {
                                if (is_array($session['payment_intent']['latest_charge'])) {
                                    $chargeId = trim((string) ($session['payment_intent']['latest_charge']['id'] ?? ''));
                                } else {
                                    $chargeId = trim((string) $session['payment_intent']['latest_charge']);
                                }
                            }
                        } else {
                            $paymentIntentId = trim((string) $session['payment_intent']);
                        }
                    }

                    $customerEmail = trim((string) ($meta['customer_email'] ?? ($session['customer_email'] ?? '')));
                    $userId     = (int) ($meta['user_id']    ?? 0);
                    $productId  = (int) ($meta['product_id'] ?? 0);
                    $quantity   = max(1, (int) ($meta['quantity']  ?? 1));
                    $unitPrice  = (float) ($meta['unit_price']  ?? 0);
                    $fullName   = trim((string) ($meta['full_name']     ?? ''));
                    $phone      = trim((string) ($meta['phone']         ?? ''));
                    $addrLine1  = trim((string) ($meta['address_line1'] ?? ''));
                    $addrLine2  = trim((string) ($meta['address_line2'] ?? ''));
                    $city       = trim((string) ($meta['shipping_city']        ?? ''));
                    $state      = trim((string) ($meta['shipping_state']       ?? ''));
                    $postalCode = trim((string) ($meta['shipping_postal_code'] ?? ''));

                    $totalAmount = round($unitPrice * $quantity, 2);
                    if ($totalAmount <= 0) {
                        $totalAmount = round((float) ($session['amount_total'] ?? 0) / 100, 2);
                    }

                    $addressLine = $addrLine1;
                    if ($addrLine2 !== '') {
                        $addressLine .= ', ' . $addrLine2;
                    }

                    // Insert address
                    $addressId = 0;
                    if ($userId > 0 && $addressLine !== '') {
                        $this->db->insert('addresses', array(
                            'user_id'      => $userId,
                            'full_name'    => $fullName,
                            'phone'        => $phone,
                            'address_line' => $addressLine,
                            'city'         => $city,
                            'state'        => $state,
                            'pincode'      => $postalCode,
                            'created_at'   => date('Y-m-d H:i:s'),
                        ));
                        $addressId = (int) $this->db->insert_id();
                    }

                    // Insert order
                    $orderId = 0;
                    if ($userId > 0) {
                        $this->db->insert('orders', array(
                            'user_id'           => $userId,
                            'address_id'        => $addressId,
                            'total_amount'      => $totalAmount,
                            'status'            => 'paid',
                            'payment_method'    => 'stripe',
                            'stripe_session_id' => $sessionId,
                            'stripe_payment_intent_id' => $paymentIntentId !== '' ? $paymentIntentId : NULL,
                            'stripe_charge_id'  => $chargeId !== '' ? $chargeId : NULL,
                            'created_at'        => date('Y-m-d H:i:s'),
                        ));
                        $orderId = (int) $this->db->insert_id();
                    }

                    // Insert order item and decrement stock
                    if ($orderId > 0 && $productId > 0) {
                        $this->db->insert('order_items', array(
                            'order_id'   => $orderId,
                            'product_id' => $productId,
                            'quantity'   => $quantity,
                            'price'      => $unitPrice,
                        ));

                        $this->Product_model->decrementStock($productId, $quantity);
                    }

                    // Log successful Stripe payment and order creation
                    if ($orderId > 0) {
                        $this->Logs_model->insert('stripe_payment_success', array(
                            'stripe_session_id'        => $sessionId,
                            'stripe_payment_intent_id' => $paymentIntentId,
                            'stripe_charge_id'         => $chargeId,
                            'product_id'               => $productId,
                            'quantity'                 => $quantity,
                            'unit_price'               => $unitPrice,
                            'total_amount'             => $totalAmount,
                        ), $userId, $orderId);
                    }

                    // Send order confirmation email to checkout email entered by user.
                    if ($orderId > 0) {
                        $this->sendOrderSuccessEmail($customerEmail, array(
                            'order_id' => $orderId,
                            'total_amount' => $totalAmount,
                            'payment_method' => 'stripe',
                            'stripe_session_id' => $sessionId,
                            'stripe_payment_intent_id' => $paymentIntentId,
                            'stripe_charge_id' => $chargeId,
                        ));
                    }
                }
            }
        }

        $data = array(
            'title'      => 'Payment Successful',
            'session_id' => $sessionId,
        );

        $this->load->view('user/payment_success', $data);
    }

    public function myOrders()
    {
        $isAdminLoggedIn = (bool) $this->session->userdata('admin_logged_in');
        $isUserLoggedIn  = (bool) $this->session->userdata('user_logged_in');

        if (!$isUserLoggedIn) {
            if ($isAdminLoggedIn) {
                redirect('admin/orders');
                return;
            }

            $this->session->set_flashdata('auth_error', 'Please log in to view your orders.');
            redirect('login');
            return;
        }

        $userId = (int) $this->session->userdata('user_id');
        $data = array(
            'title' => 'My Orders',
            'account_name' => (string) $this->session->userdata('user_name'),
            'account_email' => (string) $this->session->userdata('user_email'),
            'orders' => $this->Order_model->getOrdersForUser($userId, 100),
        );

        $this->load->view('user/my_orders', $data);
    }

    public function invoice($orderId = 0)
    {
        $isAdminLoggedIn = (bool) $this->session->userdata('admin_logged_in');
        $isUserLoggedIn  = (bool) $this->session->userdata('user_logged_in');

        if (!$isUserLoggedIn) {
            if ($isAdminLoggedIn) {
                redirect('admin/orders/invoice/' . (int) $orderId);
                return;
            }

            $this->session->set_flashdata('auth_error', 'Please log in to view invoice.');
            redirect('login');
            return;
        }

        $orderId = (int) $orderId;
        if ($orderId < 1) {
            show_404();
            return;
        }

        $userId = (int) $this->session->userdata('user_id');
        $order = $this->Order_model->getOrderDetailsForUser($orderId, $userId);
        if (!$order) {
            show_404();
            return;
        }

        $data = array(
            'title' => 'Invoice #' . (int) $order['id'],
            'order' => $order,
            'viewer_role' => 'user',
            'viewer_name' => (string) $this->session->userdata('user_name')
        );

        $this->load->view('invoice/order_invoice', $data);
    }

    public function receipt($orderId = 0)
    {
        $isAdminLoggedIn = (bool) $this->session->userdata('admin_logged_in');
        $isUserLoggedIn  = (bool) $this->session->userdata('user_logged_in');

        if (!$isUserLoggedIn) {
            if ($isAdminLoggedIn) {
                redirect('admin/orders/receipt/' . (int) $orderId);
                return;
            }

            $this->session->set_flashdata('auth_error', 'Please log in to view receipt.');
            redirect('login');
            return;
        }

        $orderId = (int) $orderId;
        if ($orderId < 1) {
            show_404();
            return;
        }

        $userId = (int) $this->session->userdata('user_id');
        $order = $this->Order_model->getOrderDetailsForUser($orderId, $userId);
        if (!$order) {
            show_404();
            return;
        }

        $data = array(
            'title' => 'Receipt #' . (int) $order['id'],
            'order' => $order,
            'viewer_role' => 'user',
            'viewer_name' => (string) $this->session->userdata('user_name')
        );

        $this->load->view('invoice/order_receipt', $data);
    }

    private function sendOrderSuccessEmail($toEmail, array $orderData)
    {
        $toEmail = trim((string) $toEmail);
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return FALSE;
        }

        $orderId = (int) ($orderData['order_id'] ?? 0);
        $totalAmount = (float) ($orderData['total_amount'] ?? 0);
        $paymentMethod = (string) ($orderData['payment_method'] ?? 'stripe');
        $sessionId = (string) ($orderData['stripe_session_id'] ?? '');
        $paymentIntentId = (string) ($orderData['stripe_payment_intent_id'] ?? '');
        $chargeId = (string) ($orderData['stripe_charge_id'] ?? '');

        $receiptUrl = site_url('my-orders/receipt/' . $orderId);
        $invoiceUrl = site_url('my-orders/invoice/' . $orderId);

        $smtpUser = $this->config->item('smtp_user') ?: '';
        $smtpPass = $this->config->item('smtp_pass') ?: '';
        $smtpHost = $this->config->item('smtp_host') ?: 'smtp.gmail.com';
        $smtpPort = $this->config->item('smtp_port') ?: 587;
        $smtpCrypto = $this->config->item('smtp_crypto') ?: 'tls';

        if ($smtpHost === '') {
            $smtpHost = 'smtp.gmail.com';
        }
        if ($smtpPort < 1) {
            $smtpPort = 587;
        }
        if ($smtpCrypto === '') {
            $smtpCrypto = 'tls';
        }

        if ($smtpUser === '' || $smtpPass === '') {
            log_message('error', 'Order confirmation email skipped: SMTP_USER or SMTP_PASS is not configured.');
            return FALSE;
        }        

        $mailConfig = array(
            'protocol' => 'smtp',
            'smtp_host' => $smtpHost,
            'smtp_port' => $smtpPort,
            'smtp_user' => $smtpUser,
            'smtp_pass' => $smtpPass,
            'smtp_crypto' => $smtpCrypto,
            'mailtype' => 'html',
            'charset'  => 'utf-8',
            'crlf'     => "\r\n",
            'newline'  => "\r\n",
        );

        $this->load->library('email');
        $this->email->initialize($mailConfig);
        $this->email->from($smtpUser, 'DL Ideas');
        $this->email->to($toEmail);
        $this->email->subject('Order Confirmation #' . $orderId);

        $transactionId = html_escape($chargeId !== '' ? $chargeId : ($paymentIntentId !== '' ? $paymentIntentId : $sessionId));

        $message = '<!DOCTYPE html>'
            . '<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>'
            . '<body style="margin:0;padding:0;background-color:#f4f4f7;font-family:Arial,Helvetica,sans-serif;">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f4f7;padding:32px 0;">'
            . '<tr><td align="center">'
            . '<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">'

            // Header
            . '<tr><td style="background-color:#4f46e5;padding:32px 40px;text-align:center;">'
            . '<h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:700;">DL Ideas</h1>'
            . '</td></tr>'

            // Success icon and heading
            . '<tr><td style="padding:40px 40px 0;text-align:center;">'
            . '<div style="width:64px;height:64px;margin:0 auto 16px;background-color:#ecfdf5;border-radius:50%;line-height:64px;font-size:32px;">&#10003;</div>'
            . '<h2 style="margin:0 0 8px;font-size:22px;color:#111827;">Payment Successful</h2>'
            . '<p style="margin:0;font-size:15px;color:#6b7280;">Thank you for your purchase! Your order has been confirmed.</p>'
            . '</td></tr>'

            // Order summary card
            . '<tr><td style="padding:32px 40px;">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f9fafb;border-radius:8px;border:1px solid #e5e7eb;">'
            . '<tr><td style="padding:20px 24px;border-bottom:1px solid #e5e7eb;">'
            . '<h3 style="margin:0;font-size:14px;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Order Summary</h3>'
            . '</td></tr>'

            . '<tr><td style="padding:16px 24px;">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0">'
            . '<tr>'
            . '<td style="padding:8px 0;font-size:14px;color:#6b7280;">Order ID</td>'
            . '<td style="padding:8px 0;font-size:14px;color:#111827;text-align:right;font-weight:600;">#' . (int) $orderId . '</td>'
            . '</tr>'
            . '<tr>'
            . '<td style="padding:8px 0;font-size:14px;color:#6b7280;">Payment Method</td>'
            . '<td style="padding:8px 0;font-size:14px;color:#111827;text-align:right;">' . html_escape(ucfirst($paymentMethod)) . '</td>'
            . '</tr>'
            . '<tr>'
            . '<td style="padding:8px 0;font-size:14px;color:#6b7280;">Transaction ID</td>'
            . '<td style="padding:8px 0;font-size:13px;color:#111827;text-align:right;word-break:break-all;">' . $transactionId . '</td>'
            . '</tr>'
            . '<tr><td colspan="2" style="padding:8px 0;"><hr style="border:none;border-top:1px solid #e5e7eb;margin:0;"></td></tr>'
            . '<tr>'
            . '<td style="padding:8px 0;font-size:16px;color:#111827;font-weight:700;">Total Paid</td>'
            . '<td style="padding:8px 0;font-size:16px;color:#4f46e5;text-align:right;font-weight:700;">$' . number_format($totalAmount, 2) . '</td>'
            . '</tr>'
            . '</table>'
            . '</td></tr>'
            . '</table>'
            . '</td></tr>'

            // CTA buttons
            . '<tr><td style="padding:0 40px 32px;text-align:center;">'
            . '<table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 auto;">'
            . '<tr>'
            . '<td style="padding-right:12px;">'
            . '<a href="' . html_escape($receiptUrl) . '" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:600;">View Receipt</a>'
            . '</td>'
            . '<td>'
            . '<a href="' . html_escape($invoiceUrl) . '" style="display:inline-block;padding:12px 24px;background-color:#ffffff;color:#4f46e5;text-decoration:none;border-radius:6px;font-size:14px;font-weight:600;border:1px solid #4f46e5;">View Invoice</a>'
            . '</td>'
            . '</tr>'
            . '</table>'
            . '</td></tr>'

            // Footer
            . '<tr><td style="padding:24px 40px;background-color:#f9fafb;border-top:1px solid #e5e7eb;text-align:center;">'
            . '<p style="margin:0 0 4px;font-size:13px;color:#9ca3af;">If you have any questions, reply to this email or contact our support team.</p>'
            . '<p style="margin:0;font-size:12px;color:#d1d5db;">&copy; ' . date('Y') . ' DL Ideas. All rights reserved.</p>'
            . '</td></tr>'

            . '</table>'
            . '</td></tr></table>'
            . '</body></html>';

        $this->email->message($message);

        $sent = $this->email->send();
        if (!$sent) {
            log_message('error', 'Order confirmation email failed for order #' . $orderId . ' to ' . $toEmail);
        }

        return $sent;
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