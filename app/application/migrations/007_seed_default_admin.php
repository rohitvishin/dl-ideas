<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Seed_default_admin extends CI_Migration
{
    private $defaultEmail = 'admin@example.com';

    public function up()
    {
        $existing = $this->db
            ->from('users')
            ->where('email', $this->defaultEmail)
            ->limit(1)
            ->count_all_results();

        if ($existing > 0) {
            return;
        }

        $this->db->insert('users', array(
            'name' => 'System Admin',
            'email' => $this->defaultEmail,
            'password' => password_hash('Admin@12345', PASSWORD_DEFAULT),
            'role' => 'admin',
            'phone' => NULL,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ));
    }

    public function down()
    {
        $this->db
            ->where('email', $this->defaultEmail)
            ->where('role', 'admin')
            ->delete('users');
    }
}
