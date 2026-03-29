<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_user_model extends CI_Model
{
    public function findActiveUserByEmail($email)
    {
        return $this->db
            ->select('id, name, email, password, role, status')
            ->from('users')
            ->where('email', $email)
            ->where('status', 1)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function findActiveAdminByEmail($email)
    {
        return $this->db
            ->select('id, name, email, password, role, status')
            ->from('users')
            ->where('email', $email)
            ->where('role', 'admin')
            ->where('status', 1)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function updatePasswordHash($userId, $passwordHash)
    {
        return $this->db
            ->where('id', $userId)
            ->update('users', array('password' => $passwordHash));
    }

    public function createUser($data)
    {
        return $this->db->insert('users', $data);
    }

    public function getRecentUsers($limit = 10)
    {
        return $this->db
            ->select('id, name, email, role, status, created_at')
            ->from('users')
            ->order_by('id', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result_array();
    }

    public function countAllUsers()
    {
        return (int) $this->db->count_all('users');
    }
}
