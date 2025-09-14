<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notes extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Note_model');
        $this->load->helper('url');
    }

    public function profile() {
        if (!$this->session->userdata('user_id')) redirect('login');
        $this->load->view('profile');
    }

    public function public() {
        $this->load->view('public');
    }

    // AJAX: fetch user notes
    public function fetch_user() {
        if (!$this->session->userdata('user_id')) {
            echo json_encode(['status'=>'error','notes'=>[],'total_pages'=>0,'current_page'=>1]);
            return;
        }
        $page = (int)$this->input->get('page') ?: 1;
        $per_page = 5;
        $user_id = $this->session->userdata('user_id');

        $notes = $this->Note_model->get_user_notes($user_id, $per_page, ($page-1)*$per_page);
        $total = $this->Note_model->count_user_notes($user_id);
        $total_pages = max(1, (int)ceil($total / $per_page));

        echo json_encode(['status'=>'success','notes'=>$notes,'total_pages'=>$total_pages,'current_page'=>$page]);
    }

    // AJAX: fetch public notes
    public function fetch_public() {
        $page = (int)$this->input->get('page') ?: 1;
        $per_page = 5;

        $notes = $this->Note_model->get_public_notes($per_page, ($page-1)*$per_page);
        $total = $this->Note_model->count_public_notes();
        $total_pages = max(1, (int)ceil($total / $per_page));

        echo json_encode(['status'=>'success','notes'=>$notes,'total_pages'=>$total_pages,'current_page'=>$page]);
    }

    // AJAX: add note
    public function add() {
        if (!$this->session->userdata('user_id')) { echo json_encode(['status'=>'error','message'=>'Not logged in']); return; }
        $data = [
            'user_id'   => $this->session->userdata('user_id'),
            'title'     => $this->input->post('title', TRUE),
            'content'   => $this->input->post('content', TRUE),
            'is_public' => $this->input->post('is_public') ? 1 : 0,
            'created_at'=> date('Y-m-d H:i:s')
        ];
        $ok = $this->Note_model->add($data);
        echo json_encode($ok ? ['status'=>'success','message'=>'Note added'] : ['status'=>'error','message'=>'Add failed']);
    }

    // AJAX: edit note
    public function edit($id = null) {
        if (!$this->session->userdata('user_id')) { echo json_encode(['status'=>'error','message'=>'Not logged in']); return; }
        $id = (int)$id;
        if (!$id) { echo json_encode(['status'=>'error','message'=>'Invalid id']); return; }
        $note = $this->Note_model->get_by_id($id);
        if (!$note || $note->user_id != $this->session->userdata('user_id')) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); return; }

        $data = [
            'title'     => $this->input->post('title', TRUE),
            'content'   => $this->input->post('content', TRUE),
            'is_public' => $this->input->post('is_public') ? 1 : 0
        ];
        $ok = $this->Note_model->update($id, $data);
        echo json_encode($ok ? ['status'=>'success','message'=>'Note updated'] : ['status'=>'error','message'=>'Update failed']);
    }

    // AJAX: delete note
    public function delete($id = null) {
        if (!$this->session->userdata('user_id')) { echo json_encode(['status'=>'error','message'=>'Not logged in']); return; }
        $id = (int)$id;
        $note = $this->Note_model->get_by_id($id);
        if (!$note || $note->user_id != $this->session->userdata('user_id')) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); return; }
        $ok = $this->Note_model->delete($id);
        echo json_encode($ok ? ['status'=>'success','message'=>'Note deleted'] : ['status'=>'error','message'=>'Delete failed']);
    }
}
