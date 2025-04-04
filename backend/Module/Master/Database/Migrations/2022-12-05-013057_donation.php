<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Donation extends Migration
{
    public function up()
    {
        $fields = [
            'amount_paid' => ['type' => 'FLOAT', 'constraint' => '10,2', 'default' => 0],
        ];
        $forge->addColumn('sponsorship', $fields);
        $fields = [
            'id'          => [
                'type'           => 'BIGINT',
                'constraint'     => 25,
                'auto_increment' => true,
            ],
            'receipt_id'       => [
                'type'       => 'VARCHAR',
                'constraint' => '65',
                //'unique'     => true,
            ],
            'amount'      => [
                'type'       => 'FLOAT',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status'      => [
                'type'       => 'ENUM',
                'constraint' => ['publish', 'pending', 'draft'],
                'default'    => 'pending',
            ],
        ];
        $this->forge->addField([
            'lat' => [
                'type'       => 'DECIMAL',
                'constraint' => '2,5',
                'null'       => true,
                'default'    => null,
            ],
            'lat' => [
                'type'       => 'DECIMAL',
                'constraint' => '2,5',
                'null'       => true,
                'default'    => null,
            ],
        ]);
        $this->forge->createTable('donation', true);
    }

    public function down()
    {
        $this->forge->dropTable('donation', true);
    }
}
