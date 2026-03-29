<?php

class Migration_Create_addresses extends CI_Migration {

    public function up()
    {
        $this->load->dbforge();

        $this->dbforge->add_field([
            'id' => ['type'=>'INT','unsigned'=>TRUE,'auto_increment'=>TRUE],
            'user_id' => ['type'=>'INT','unsigned'=>TRUE],
            'full_name' => ['type'=>'VARCHAR','constraint'=>100],
            'phone' => ['type'=>'VARCHAR','constraint'=>20],
            'address_line' => ['type'=>'TEXT'],
            'city' => ['type'=>'VARCHAR','constraint'=>100],
            'state' => ['type'=>'VARCHAR','constraint'=>100],
            'pincode' => ['type'=>'VARCHAR','constraint'=>10],
            'created_at' => ['type'=>'DATETIME'],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('addresses');
    }

    public function down()
    {
        $this->dbforge->drop_table('addresses');
    }
}