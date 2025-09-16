<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notes extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Note_model');
        $this->load->helper('url');
        $this->load->library('session');
    }

    // Page: user's notes (requires login)
    public function profile() {
        if (!$this->session->userdata('user_id')) redirect('login');
        $this->load->view('profile');
    }

    // Page: public notes (no login required)
    public function public() {
        $this->load->view('public');
    }

    /**
     * Central AJAX endpoint for notes actions.
     * Uses parameter 'action' (GET or POST). Switch/case handles behaviors:
     * - fetch_user (GET): returns user's notes + pagination
     * - fetch_public (GET): returns public notes + pagination (includes username)
     * - add (POST): create a note (requires login)
     * - edit (POST): edit a note (requires login, owner-only)
     * - delete (POST/GET): delete a note (requires login, owner-only)
     *
     * All responses are JSON.
     */
    public function ajax() {
        $this->output->set_content_type('application/json');

        $action = $this->input->post('action');
        if (!$action) $action = $this->input->get('action');

        if (!$action) {
            echo json_encode(['status' => 'error', 'message' => 'No action specified']);
            return;
        }

        switch ($action) {
            case 'fetch_user':
                $this->_ajax_fetch_user();
                break;

            case 'fetch_public':
                $this->_ajax_fetch_public();
                break;

            case 'add':
                $this->_ajax_add();
                break;

            case 'edit':
                $this->_ajax_edit();
                break;

            case 'delete':
                $this->_ajax_delete();
                break;

            default:
                echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
                break;
        }
    }

    // PRIVATE: fetch current user's notes (AJAX)
    private function _ajax_fetch_user() {
        if (!$this->session->userdata('user_id')) {
            echo json_encode(['status'=>'error','message'=>'Not logged in']);
            return;
        }
        $user_id = (int)$this->session->userdata('user_id');
        $page = max(1, (int)$this->input->get('page'));
        $per_page = 6;

        $total = $this->Note_model->count_user_notes($user_id);
        $total_pages = (int)ceil($total / $per_page);
        $offset = ($page - 1) * $per_page;
        $notes = $this->Note_model->get_user_notes($user_id, $per_page, $offset);

        echo json_encode(['status'=>'success','notes'=>$notes,'total_pages'=>$total_pages,'page'=>$page]);
    }

    // PRIVATE: fetch public notes (AJAX) — includes username
    private function _ajax_fetch_public() {
        $page = max(1, (int)$this->input->get('page'));
        $per_page = 8;

        $total = $this->Note_model->count_public_notes();
        $total_pages = (int)ceil($total / $per_page);
        $offset = ($page - 1) * $per_page;
        $notes = $this->Note_model->get_public_notes($per_page, $offset);

        echo json_encode(['status'=>'success','notes'=>$notes,'total_pages'=>$total_pages,'page'=>$page]);
    }

    // PRIVATE: add a note (AJAX POST)
    private function _ajax_add() {
        if (!$this->session->userdata('user_id')) {
            echo json_encode(['status'=>'error','message'=>'Not logged in']);
            return;
        }

        $title = trim($this->input->post('title'));
        $content = trim($this->input->post('content'));
        $is_public = $this->input->post('is_public') ? 1 : 0;

        if ($title === '' || $content === '') {
            echo json_encode(['status'=>'error','message'=>'Title and content cannot be empty']);
            return;
        }

        $data = [
            'user_id'    => (int)$this->session->userdata('user_id'),
            'title'      => $title,
            'content'    => $content,
            'is_public'  => $is_public,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $insert_id = $this->Note_model->add($data);

        if ($insert_id) {
            $note = $this->Note_model->get_by_id($insert_id);
            echo json_encode(['status'=>'success','message'=>'Note added','note'=>$note]);
        } else {
            echo json_encode(['status'=>'error','message'=>'Insert failed']);
        }
    }

    // PRIVATE: edit a note (AJAX POST)
    private function _ajax_edit() {
        if (!$this->session->userdata('user_id')) {
            echo json_encode(['status'=>'error','message'=>'Not logged in']);
            return;
        }
        $user_id = (int)$this->session->userdata('user_id');

        $id = (int)$this->input->post('id');
        if (!$id) { echo json_encode(['status'=>'error','message'=>'Invalid ID']); return; }

        $note = $this->Note_model->get_by_id($id);
        if (!$note) { echo json_encode(['status'=>'error','message'=>'Note not found']); return; }
        if ((int)$note->user_id !== $user_id) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); return; }

        $title = trim($this->input->post('title'));
        $content = trim($this->input->post('content'));
        $is_public = $this->input->post('is_public') ? 1 : 0;

        if ($title === '' || $content === '') {
            echo json_encode(['status'=>'error','message'=>'Title and content cannot be empty']);
            return;
        }

        $data = [
            'title' => $title,
            'content' => $content,
            'is_public' => $is_public,
        ];

        $ok = $this->Note_model->update($id, $data);
        if ($ok) {
            $note = $this->Note_model->get_by_id($id);
            echo json_encode(['status'=>'success','message'=>'Note updated','note'=>$note]);
        } else {
            echo json_encode(['status'=>'error','message'=>'Update failed']);
        }
    }

    // PRIVATE: delete a note (AJAX POST or GET)
    private function _ajax_delete() {
        if (!$this->session->userdata('user_id')) {
            echo json_encode(['status'=>'error','message'=>'Not logged in']);
            return;
        }
        $user_id = (int)$this->session->userdata('user_id');

        // allow ID from POST or GET
        $id = (int)$this->input->post('id');
        if (!$id) $id = (int)$this->input->get('id');

        if (!$id) { echo json_encode(['status'=>'error','message'=>'Invalid ID']); return; }

        $note = $this->Note_model->get_by_id($id);
        if (!$note) { echo json_encode(['status'=>'error','message'=>'Note not found']); return; }
        if ((int)$note->user_id !== $user_id) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); return; }

        $ok = $this->Note_model->delete($id);
        echo json_encode($ok ? ['status'=>'success','message'=>'Note deleted'] : ['status'=>'error','message'=>'Delete failed']);
    }
}
