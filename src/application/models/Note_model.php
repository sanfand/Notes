<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Note_model extends CI_Model {
    private $table = 'notes';

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    /**
     * Insert a new note.
     * $data must include: user_id, title, content, is_public, created_at
     */
    public function add($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Get notes for a user (pagination)
     */
    public function get_user_notes($user_id, $limit=6, $offset=0) {
        return $this->db
                    ->where('user_id', $user_id)
                    ->order_by('created_at', 'DESC')
                    ->get($this->table, $limit, $offset)
                    ->result();
    }

    /**
     * Count user's notes (for pagination)
     */
    public function count_user_notes($user_id) {
        return (int)$this->db->where('user_id', $user_id)->from($this->table)->count_all_results();
    }

    /**
     * Get public notes (pagination) — includeing username from users table
     */
    public function get_public_notes($limit=8, $offset=0) {
        $this->db->select('notes.*, users.username');
        $this->db->from($this->table);
        $this->db->join('users', 'users.id = notes.user_id', 'left');
        $this->db->where('notes.is_public', 1);
        $this->db->order_by('notes.created_at', 'DESC');
        $this->db->limit((int)$limit, (int)$offset);
        return $this->db->get()->result();
    }

    /**
     * Count public notes
     */
    public function count_public_notes() {
        return (int)$this->db->where('is_public', 1)->from($this->table)->count_all_results();
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
