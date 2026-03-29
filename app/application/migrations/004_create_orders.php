<?php

class Migration_Create_orders extends CI_Migration {

    public function up()
    {
        $this->load->dbforge();

        $this->dbforge->add_field([
            'id' => ['type'=>'INT','unsigned'=>TRUE,'auto_increment'=>TRUE],
            'user_id' => ['type'=>'INT','unsigned'=>TRUE],
            'address_id' => ['type'=>'INT','unsigned'=>TRUE],
            'total_amount' => ['type'=>'DECIMAL','constraint'=>'10,2'],
            'status' => [
                'type' => 'ENUM("pending","paid","shipped","delivered","cancelled")',
                'default' => 'pending'
            ],
            'payment_method' => ['type'=>'VARCHAR','constraint'=>50],
            'created_at' => ['type'=>'DATETIME'],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('orders');
    }

    public function down()
    {
        $this->dbforge->drop_table('orders');
    }
}