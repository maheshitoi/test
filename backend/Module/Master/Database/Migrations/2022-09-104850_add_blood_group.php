<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBloodGroup extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => [
                'type'           => 'INT',
                'constraint'     => 16,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'blood_groupName'    => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'status'     => [
                'type'       => 'ENUM',
                'constraint' => ['1', '0'],
                'default'    => '1',
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('blood_group', true);
        $this->forge->addField([
            'id'         => [
                'type'           => 'INT',
                'constraint'     => 16,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'alive_statusName'    => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'status'     => [
                'type'       => 'ENUM',
                'constraint' => ['1', '0'],
                'default'    => '1',
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('alive_status', true);
    }

    public function down()
    {
        $this->forge->dropTable('blood_group', true);
        $this->forge->dropTable('alive_status', true);
    }
}
