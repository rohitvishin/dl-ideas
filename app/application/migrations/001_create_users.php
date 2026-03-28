<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_users extends CI_Migration {

    public function up()
    {
        $this->load->dbforge();

        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE
            ],
            'name' => [
                'type' => 'VARCHAR', 'constraint' => 100
            ],
            'email' => [
                'type' => 'VARCHAR', 'constraint' => 150, 'unique' => TRUE
            ],
            'password' => [
                'type' => 'VARCHAR', 'constraint' => 255
            ],
            'role' => [
                'type' => 'ENUM("user","admin")', 'default' => 'user'
            ],
            'phone' => [
                'type' => 'VARCHAR', 'constraint' => 20, 'null' => TRUE
            ],
            'status' => [
                'type' => 'TINYINT', 'default' => 1
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('users');
    }

    public function down()
    {
        $this->dbforge->drop_table('users');
    }
}