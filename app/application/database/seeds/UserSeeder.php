<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserSeeder {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function run()
    {
        $data = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Test User',
                'email' => 'user@example.com',
                'password' => password_hash('user123', PASSWORD_BCRYPT),
                'role' => 'user',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->CI->db->insert_batch('users', $data);
    }
}