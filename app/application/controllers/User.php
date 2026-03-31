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
        $this->load->model('Admin_order_model');
    }

    public function index()
    {
        $products = $this->Admin_product_model->getCatalogProducts();
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

        // Validate session_id format to prevent SSRF
        if (!preg_match('/^cs_(test|live)_[a-zA-Z0-9]+$/', $sessionId)) {
            redirect('user');
            return;
        }

        $this->load->model('Admin_order_model');

        // Idempotency: if already processed, skip insertion
        $alreadyProcessed = $this->Admin_order_model->orderExistsByStripeSession($sessionId);

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

                        $this->Admin_product_model->decrementStock($productId, $quantity);
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
            'orders' => $this->Admin_order_model->getOrdersForUser($userId, 100),
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
        $order = $this->Admin_order_model->getOrderDetailsForUser($orderId, $userId);
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
        $order = $this->Admin_order_model->getOrderDetailsForUser($orderId, $userId);
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
        $this->email->from($smtpUser, 'Ecom Nova');
        $this->email->to($toEmail);
        $this->email->subject('Order Confirmation #' . $orderId);

        $message = '<h2>Payment Successful</h2>'
            . '<p>Your order has been placed successfully.</p>'
            . '<p><strong>Order ID:</strong> #' . (int) $orderId . '</p>'
            . '<p><strong>Total Paid:</strong> $' . number_format($totalAmount, 2) . '</p>'
            . '<p><strong>Payment Method:</strong> ' . html_escape($paymentMethod) . '</p>'
            . '<p><strong>Transaction ID:</strong> ' . html_escape($chargeId !== '' ? $chargeId : ($paymentIntentId !== '' ? $paymentIntentId : $sessionId)) . '</p>'
            . '<p><strong>Payment Intent ID:</strong> ' . html_escape($paymentIntentId !== '' ? $paymentIntentId : 'N/A') . '</p>'
            . '<p><strong>Charge ID:</strong> ' . html_escape($chargeId !== '' ? $chargeId : 'N/A') . '</p>'
            . '<p><a href="' . html_escape($receiptUrl) . '">View Receipt</a> | <a href="' . html_escape($invoiceUrl) . '">View Invoice</a></p>';

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