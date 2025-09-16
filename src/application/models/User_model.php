<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_user_by_email($email) {
        return $this->db->get_where('users', ['email' => $email])->row();
    }

    public function get_user_by_username($username) {
        return $this->db->get_where('users', ['username' => $username])->row();
    }

    public function insert_user($data) {
        return $this->db->insert('users', $data);
    }

    public function email_exists($email) {
        return $this->db->where('email', $email)->count_all_results('users') > 0;
    }

    public function username_exists($username) {
        return $this->db->where('username', $username)->count_all_results('users') > 0;
    }
}
