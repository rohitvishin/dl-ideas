<?php

class Migration_Create_products extends CI_Migration {

    public function up()
    {
        $this->load->dbforge();

        $this->dbforge->add_field([
            'id' => ['type'=>'INT','unsigned'=>TRUE,'auto_increment'=>TRUE],
            'name' => ['type'=>'VARCHAR','constraint'=>150],
            'slug' => ['type'=>'VARCHAR','constraint'=>150],
            'description' => ['type'=>'TEXT','null'=>TRUE],
            'price' => ['type'=>'DECIMAL','constraint'=>'10,2'],
            'stock' => ['type'=>'INT','default'=>0],
            'image' => ['type'=>'VARCHAR','constraint'=>255],
            'status' => ['type'=>'TINYINT','default'=>1],
            'created_at' => ['type'=>'DATETIME'],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('products');
    }

    public function down()
    {
        $this->dbforge->drop_table('products');
    }
}