<?php
class Migration_Create_logs extends CI_Migration {

    public function up()
    {
        $this->load->dbforge();

        $this->dbforge->add_field([
            'id' => ['type'=>'INT','unsigned'=>TRUE,'auto_increment'=>TRUE],
            'user_id' => ['type'=>'INT','unsigned'=>TRUE,'null'=>TRUE],
            'order_id' => ['type'=>'INT','unsigned'=>TRUE,'null'=>TRUE],
            'logs' => ['type'=>'TEXT','null'=>TRUE],
            'ip_address' => ['type'=>'VARCHAR','constraint'=>45],
            'created_at' => ['type'=>'DATETIME'],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('logs');
    }

    public function down()
    {
        $this->dbforge->drop_table('logs');
    }
}