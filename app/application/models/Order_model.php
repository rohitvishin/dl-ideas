<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model
{
    public function countAllOrders()
    {
        return (int) $this->db->count_all('orders');
    }

    public function orderExistsByStripeSession($sessionId)
    {
        return $this->db
            ->from('orders')
            ->where('stripe_session_id', $sessionId)
            ->limit(1)
            ->count_all_results() > 0;
    }

    public function getOrdersForUser($userId, $limit = 50)
    {
        $orders = $this->db
            ->select('o.id, o.user_id, o.address_id, o.total_amount, o.status, o.payment_method, o.stripe_session_id, o.stripe_payment_intent_id, o.stripe_charge_id, o.created_at')
            ->select('a.full_name AS shipping_name, a.phone AS shipping_phone, a.address_line AS shipping_address, a.city AS shipping_city, a.state AS shipping_state, a.pincode AS shipping_pincode')
            ->from('orders o')
            ->join('addresses a', 'a.id = o.address_id', 'left')
            ->where('o.user_id', (int) $userId)
            ->order_by('o.id', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result_array();

        if (empty($orders)) {
            return array();
        }

        return $this->attachOrderItems($orders);
    }

    public function getRecentOrdersForAdmin($limit = 200)
    {
        $orders = $this->db
            ->select('o.id, o.user_id, o.address_id, o.total_amount, o.status, o.payment_method, o.stripe_session_id, o.stripe_payment_intent_id, o.stripe_charge_id, o.created_at')
            ->select('u.name AS user_name, u.email AS user_email')
            ->select('a.full_name AS shipping_name, a.city AS shipping_city, a.state AS shipping_state, a.pincode AS shipping_pincode')
            ->from('orders o')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->join('addresses a', 'a.id = o.address_id', 'left')
            ->order_by('o.id', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result_array();

        if (empty($orders)) {
            return array();
        }

        return $this->attachOrderItems($orders);
    }

    public function getOrderDetailsForUser($orderId, $userId)
    {
        $order = $this->db
            ->select('o.id, o.user_id, o.address_id, o.total_amount, o.status, o.payment_method, o.stripe_session_id, o.stripe_payment_intent_id, o.stripe_charge_id, o.created_at')
            ->select('u.name AS user_name, u.email AS user_email')
            ->select('a.full_name AS shipping_name, a.phone AS shipping_phone, a.address_line AS shipping_address, a.city AS shipping_city, a.state AS shipping_state, a.pincode AS shipping_pincode')
            ->from('orders o')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->join('addresses a', 'a.id = o.address_id', 'left')
            ->where('o.id', (int) $orderId)
            ->where('o.user_id', (int) $userId)
            ->limit(1)
            ->get()
            ->row_array();

        if (empty($order)) {
            return NULL;
        }

        $orders = $this->attachOrderItems(array($order));
        return $orders[0];
    }

    public function getOrderDetailsForAdmin($orderId)
    {
        $order = $this->db
            ->select('o.id, o.user_id, o.address_id, o.total_amount, o.status, o.payment_method, o.stripe_session_id, o.stripe_payment_intent_id, o.stripe_charge_id, o.created_at')
            ->select('u.name AS user_name, u.email AS user_email')
            ->select('a.full_name AS shipping_name, a.phone AS shipping_phone, a.address_line AS shipping_address, a.city AS shipping_city, a.state AS shipping_state, a.pincode AS shipping_pincode')
            ->from('orders o')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->join('addresses a', 'a.id = o.address_id', 'left')
            ->where('o.id', (int) $orderId)
            ->limit(1)
            ->get()
            ->row_array();

        if (empty($order)) {
            return NULL;
        }

        $orders = $this->attachOrderItems(array($order));
        return $orders[0];
    }

    private function attachOrderItems(array $orders)
    {
        $orderIds = array();
        foreach ($orders as $order) {
            $orderIds[] = (int) $order['id'];
        }

        $items = $this->db
            ->select('oi.order_id, oi.product_id, oi.quantity, oi.price, p.name AS product_name')
            ->from('order_items oi')
            ->join('products p', 'p.id = oi.product_id', 'left')
            ->where_in('oi.order_id', $orderIds)
            ->order_by('oi.id', 'ASC')
            ->get()
            ->result_array();

        $itemsByOrderId = array();
        foreach ($items as $item) {
            $oid = (int) $item['order_id'];
            if (!isset($itemsByOrderId[$oid])) {
                $itemsByOrderId[$oid] = array();
            }

            $itemsByOrderId[$oid][] = array(
                'product_id' => (int) $item['product_id'],
                'product_name' => (string) $item['product_name'],
                'quantity' => (int) $item['quantity'],
                'price' => (float) $item['price'],
            );
        }

        foreach ($orders as $index => $order) {
            $oid = (int) $order['id'];
            $orders[$index]['items'] = isset($itemsByOrderId[$oid]) ? $itemsByOrderId[$oid] : array();
        }

        return $orders;
    }
}
