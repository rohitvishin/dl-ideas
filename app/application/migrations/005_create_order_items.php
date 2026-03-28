<?php

class Migration_Create_order_items extends CI_Migration {

    public function up()
    {
        $this->load->dbforge();

        $this->dbforge->add_field([
            'id' => ['type'=>'INT','unsigned'=>TRUE,'auto_increment'=>TRUE],
            'order_id' => ['type'=>'INT','unsigned'=>TRUE],
            'product_id' => ['type'=>'INT','unsigned'=>TRUE],
            'quantity' => ['type'=>'INT'],
            'price' => ['type'=>'DECIMAL','constraint'=>'10,2'],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('order_items');
    }

    public function down()
    {
        $this->dbforge->drop_table('order_items');
    }
}