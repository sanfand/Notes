<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    private $table = 'users';
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_by_username_or_email($val) {
        return $this->db
            ->group_start()
              ->where('username', $val)
              ->or_where('email', $val)
            ->group_end()
            ->get($this->table)
            ->row();
    }

    public function insert($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    // optional helper to upgrade legacy passwords
    public function update_password($user_id, $hashed) {
        return $this->db->where('id', $user_id)->update($this->table, ['password' => $hashed]);
    }
}
