<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function create($data) {
        return $this->db->insert('users', $data);
    }

    public function get_by_credentials($username, $password) {
        $this->db->group_start();
        $this->db->where('username', $username);
        $this->db->or_where('email', $username);
        $this->db->group_end();
        $this->db->where('password', $password);
        return $this->db->get('users')->row();
    }

    public function get($id) {
        return $this->db->get_where('users', ['id' => $id])->row();
    }
}
