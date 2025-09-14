<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Note_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function add($data) {
        return $this->db->insert('notes', $data);
    }

    public function edit($id, $data, $user_id) {
        return $this->db->where(['id'=>$id, 'user_id'=>$user_id])->update('notes', $data);
    }

    public function delete($id, $user_id) {
        return $this->db->where(['id'=>$id, 'user_id'=>$user_id])->delete('notes');
    }

    public function get_user_notes($user_id, $page=1, $limit=5) {
        $offset = ($page - 1) * $limit;
        $this->db->select('notes.*, users.username');
        $this->db->from('notes');
        $this->db->join('users','users.id=notes.user_id','left');
        $this->db->where('notes.user_id', $user_id);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('', $limit, $offset);
        $notes = $query->result();

        $this->db->where('user_id', $user_id);
        $count = $this->db->count_all_results('notes');
        $total_pages = $limit ? (int) ceil($count / $limit) : 1;

        return ['notes' => $notes, 'total_pages' => $total_pages];
    }

    public function get_public_notes($page=1, $limit=5) {
        $offset = ($page - 1) * $limit;
        $this->db->select('notes.*, users.username');
        $this->db->from('notes');
        $this->db->join('users','users.id=notes.user_id','left');
        $this->db->where('is_public', 1);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('', $limit, $offset);
        $notes = $query->result();

        $this->db->where('is_public', 1);
        $count = $this->db->count_all_results('notes');
        $total_pages = $limit ? (int) ceil($count / $limit) : 1;

        return ['notes' => $notes, 'total_pages' => $total_pages];
    }
}
