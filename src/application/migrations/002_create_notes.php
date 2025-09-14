<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_notes extends CI_Migration {
    public function up() {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'content' => [
                'type' => 'TEXT',
            ],
            'is_public' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_at' => [
            'type' => 'TIMESTAMP',
            'null' => FALSE,
            'default' => 'CURRENT_TIMESTAMP'
            ],

        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id'); // add index for faster joins
        $this->dbforge->create_table('notes', TRUE);

        // Add foreign key manually
        $this->db->query('
            ALTER TABLE `notes`
            ADD CONSTRAINT `fk_notes_user`
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
        ');
        $this->db->query("ALTER TABLE notes 
        MODIFY created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");

    }

    public function down() {
        // Disable FK checks so drop works cleanly
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->dbforge->drop_table('notes', TRUE);
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
