<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_api_tokens extends CI_Migration {

    public function up()
    {
        $this->load->dbforge();

        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE
            ],
            'user_id' => [
                'type' => 'INT', 'unsigned' => TRUE
            ],
            'token' => [
                'type' => 'VARCHAR', 'constraint' => 64, 'unique' => TRUE
            ],
            'expires_at' => [
                'type' => 'DATETIME'
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('api_tokens');
    }

    public function down()
    {
        $this->dbforge->drop_table('api_tokens');
    }
}
