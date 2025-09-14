<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Note_model extends CI_Model {
    private $table = 'notes';
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function get_user_notes($user_id, $limit=5, $offset=0) {
        return $this->db->where('user_id', $user_id)
                        ->order_by('created_at', 'DESC')
                        ->get($this->table, $limit, $offset)
                        ->result();
    }

    public function count_user_notes($user_id) {
        return $this->db->where('user_id', $user_id)->count_all_results($this->table);
    }

    public function get_public_notes($limit=5, $offset=0) {
        $this->db->select('notes.*, users.username')
                 ->from('notes')
                 ->join('users', 'users.id = notes.user_id', 'left')
                 ->where('is_public', 1)
                 ->order_by('created_at', 'DESC')
                 ->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_public_notes() {
        return $this->db->where('is_public', 1)->count_all_results($this->table);
    }

    public function add($data) {
        return $this->db->insert($this->table, $data);
    }

    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['id'=> $id])->row();
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function delete($id) {
        return $this->db->where('id', $id)->delete($this->table);
    }
}
