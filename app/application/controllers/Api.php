<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('User_model');
        $this->load->model('Order_model');
    }

    // ---------------------------------------------------------------
    //  POST /api/login
    // ---------------------------------------------------------------
    public function login()
    {
        if ($this->input->method(TRUE) !== 'POST') {
            return $this->jsonResponse(405, array('error' => 'Method not allowed.'));
        }

        $rawInput = json_decode(trim((string) file_get_contents('php://input')), TRUE);

        $email    = '';
        $password = '';

        if (is_array($rawInput)) {
            $email    = trim((string) ($rawInput['email'] ?? ''));
            $password = (string) ($rawInput['password'] ?? '');
        }

        // Fallback to form-encoded POST
        if ($email === '') {
            $email = trim((string) $this->input->post('email', TRUE));
        }
        if ($password === '') {
            $password = (string) $this->input->post('password', FALSE);
        }

        $email = strtolower($email);

        if ($email === '' || $password === '') {
            return $this->jsonResponse(400, array('error' => 'Email and password are required.'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonResponse(400, array('error' => 'Invalid email format.'));
        }

        $account = $this->User_model->findActiveUserByEmail($email);

        if (!$account || !$this->verifyPassword($password, $account['password'])) {
            return $this->jsonResponse(401, array('error' => 'Invalid email or password.'));
        }

        // Upgrade legacy plain-text passwords
        if ($this->isLegacyPassword($account['password'])) {
            $this->User_model->updatePasswordHash((int) $account['id'], password_hash($password, PASSWORD_DEFAULT));
        }

        // Generate a secure token, store it, and return it
        $token     = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $this->db->insert('api_tokens', array(
            'user_id'    => (int) $account['id'],
            'token'      => $token,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s'),
        ));

        return $this->jsonResponse(200, array(
            'token'      => $token,
            'expires_at' => $expiresAt,
            'user'       => array(
                'id'    => (int) $account['id'],
                'name'  => $account['name'],
                'email' => $account['email'],
                'role'  => $account['role'],
            ),
        ));
    }

    // ---------------------------------------------------------------
    //  GET /api/invoices/:id
    // ---------------------------------------------------------------
    public function invoices($orderId = 0)
    {
        if ($this->input->method(TRUE) !== 'GET') {
            return $this->jsonResponse(405, array('error' => 'Method not allowed.'));
        }

        $user = $this->authenticateToken();
        if (!$user) {
            return; // response already sent
        }

        $orderId = (int) $orderId;
        if ($orderId < 1) {
            return $this->jsonResponse(400, array('error' => 'Invalid order ID.'));
        }

        $order = $this->getOrderForUser($user, $orderId);
        if (!$order) {
            return $this->jsonResponse(404, array('error' => 'Order not found.'));
        }

        return $this->jsonResponse(200, array(
            'invoice' => array(
                'order_id'       => (int) $order['id'],
                'user'           => array(
                    'name'  => $order['user_name'] ?? '',
                    'email' => $order['user_email'] ?? '',
                ),
                'shipping'       => array(
                    'name'    => $order['shipping_name'] ?? '',
                    'phone'   => $order['shipping_phone'] ?? '',
                    'address' => $order['shipping_address'] ?? '',
                    'city'    => $order['shipping_city'] ?? '',
                    'state'   => $order['shipping_state'] ?? '',
                    'pincode' => $order['shipping_pincode'] ?? '',
                ),
                'items'          => $this->formatItems($order),
                'total_amount'   => (float) $order['total_amount'],
                'status'         => $order['status'],
                'payment_method' => $order['payment_method'],
                'created_at'     => $order['created_at'],
            ),
        ));
    }

    // ---------------------------------------------------------------
    //  GET /api/receipts/:id
    // ---------------------------------------------------------------
    public function receipts($orderId = 0)
    {
        if ($this->input->method(TRUE) !== 'GET') {
            return $this->jsonResponse(405, array('error' => 'Method not allowed.'));
        }

        $user = $this->authenticateToken();
        if (!$user) {
            return;
        }

        $orderId = (int) $orderId;
        if ($orderId < 1) {
            return $this->jsonResponse(400, array('error' => 'Invalid order ID.'));
        }

        $order = $this->getOrderForUser($user, $orderId);
        if (!$order) {
            return $this->jsonResponse(404, array('error' => 'Order not found.'));
        }

        return $this->jsonResponse(200, array(
            'receipt' => array(
                'order_id'                 => (int) $order['id'],
                'total_amount'             => (float) $order['total_amount'],
                'status'                   => $order['status'],
                'payment_method'           => $order['payment_method'],
                'stripe_session_id'        => $order['stripe_session_id'] ?? '',
                'stripe_payment_intent_id' => $order['stripe_payment_intent_id'] ?? '',
                'stripe_charge_id'         => $order['stripe_charge_id'] ?? '',
                'items'                    => $this->formatItems($order),
                'created_at'               => $order['created_at'],
            ),
        ));
    }

    // ---------------------------------------------------------------
    //  Private helpers
    // ---------------------------------------------------------------

    /**
     * Validate Bearer token from Authorization header.
     * Returns user row array on success, or NULL (and sends 401) on failure.
     */
    private function authenticateToken()
    {
        $header = $this->input->get_request_header('Authorization', TRUE);

        if (!$header || stripos($header, 'Bearer ') !== 0) {
            $this->jsonResponse(401, array('error' => 'Missing or invalid Authorization header. Use: Bearer <token>'));
            return NULL;
        }

        $token = trim(substr($header, 7));

        if ($token === '' || strlen($token) !== 64 || !ctype_xdigit($token)) {
            $this->jsonResponse(401, array('error' => 'Invalid token format.'));
            return NULL;
        }

        $row = $this->db
            ->select('t.user_id, t.expires_at, u.name, u.email, u.role, u.status')
            ->from('api_tokens t')
            ->join('users u', 'u.id = t.user_id')
            ->where('t.token', $token)
            ->limit(1)
            ->get()
            ->row_array();

        if (!$row) {
            $this->jsonResponse(401, array('error' => 'Invalid token.'));
            return NULL;
        }

        if (strtotime($row['expires_at']) < time()) {
            // Clean up expired token
            $this->db->where('token', $token)->delete('api_tokens');
            $this->jsonResponse(401, array('error' => 'Token has expired. Please login again.'));
            return NULL;
        }

        if ((int) $row['status'] !== 1) {
            $this->jsonResponse(403, array('error' => 'Account is disabled.'));
            return NULL;
        }

        return $row;
    }

    /**
     * Fetch order: admins can see any order; users only their own.
     */
    private function getOrderForUser($user, $orderId)
    {
        if ($user['role'] === 'admin') {
            return $this->Order_model->getOrderDetailsForAdmin((int) $orderId);
        }

        return $this->Order_model->getOrderDetailsForUser((int) $orderId, (int) $user['user_id']);
    }

    /**
     * Extract items array from order row (set by attachOrderItems).
     */
    private function formatItems($order)
    {
        $items = array();
        if (isset($order['items']) && is_array($order['items'])) {
            foreach ($order['items'] as $item) {
                $items[] = array(
                    'product_id'   => (int) ($item['product_id'] ?? 0),
                    'product_name' => $item['product_name'] ?? '',
                    'quantity'     => (int) ($item['quantity'] ?? 0),
                    'price'        => (float) ($item['price'] ?? 0),
                );
            }
        }
        return $items;
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

    private function jsonResponse($statusCode, $data)
    {
        $this->output
            ->set_status_header($statusCode)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}
