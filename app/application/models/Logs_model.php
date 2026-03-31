<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logs_model extends CI_Model
{
    public function insert($event, array $data = array(), $userId = NULL, $orderId = NULL)
    {
        $this->db->insert('logs', array(
            'user_id'    => $userId !== NULL ? (int) $userId : NULL,
            'order_id'   => $orderId !== NULL ? (int) $orderId : NULL,
            'logs'        => json_encode(array_merge(array('event' => $event), $data)),
            'ip_address' => $this->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s'),
        ));

        return (int) $this->db->insert_id();
    }

    public function getByOrderId($orderId)
    {
        return $this->db
            ->where('order_id', (int) $orderId)
            ->order_by('created_at', 'ASC')
            ->get('logs')
            ->result_array();
    }
}
