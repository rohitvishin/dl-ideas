<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_order_model extends CI_Model
{
    public function countAllOrders()
    {
        return (int) $this->db->count_all('orders');
    }
}
