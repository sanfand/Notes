<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Note_model');
        $this->load->helper('url');
    }

    public function profile() {
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }
        $this->load->view('frontend/profile');
    }

    public function public() {
        $this->load->view('frontend/public');
    }

    public function fetch_user() {
        if (!$this->session->userdata('user_id')) {
            echo json_encode(['notes'=>[], 'total_pages'=>0]);
            return;
        }
        $page = max(1, (int)$this->input->get('page'));
        $limit = (int)$this->input->get('limit') ?: 5;
        $user_id = $this->session->userdata('user_id');

        $result = $this->Note_model->get_user_notes($user_id, $page, $limit);
        echo json_encode($result);
    }

    public function fetch_public() {
        $page = max(1, (int)$this->input->get('page'));
        $limit = (int)$this->input->get('limit') ?: 5;

        $result = $this->Note_model->get_public_notes($page, $limit);
        echo json_encode($result);
    }

    public function add() {
        if (!$this->session->userdata('user_id')) {
            echo json_encode(['status'=>'error','message'=>'Not authenticated']);
            return;
        }
        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'title' => $this->input->post('title', TRUE),
            'content' => $this->input->post('content', TRUE),
            'is_public' => $this->input->post('is_public') ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->Note_model->add($data);
        echo json_encode(['status'=>'success']);
    }

    public function edit($id = NULL) {
        if (!$this->session->userdata('user_id')) {
            echo json_encode(['status'=>'error','message'=>'Not authenticated']);
            return;
        }
        $id = $id ?: (int)$this->input->post('id');
        if (!$id) {
            echo json_encode(['status'=>'error','message'=>'Missing id']);
            return;
        }
        $data = [
            'title' => $this->input->post('title', TRUE),
            'content' => $this->input->post('content', TRUE),
            'is_public' => $this->input->post('is_public') ? 1 : 0
        ];
        $this->Note_model->edit($id, $data, $this->session->userdata('user_id'));
        echo json_encode(['status'=>'success']);
    }

    public function delete($id = NULL) {
        if (!$this->session->userdata('user_id')) {
            echo json_encode(['status'=>'error','message'=>'Not authenticated']);
            return;
        }
        $id = $id ?: (int)$this->input->post('id');
        if (!$id) {
            echo json_encode(['status'=>'error','message'=>'Missing id']);
            return;
        }
        $this->Note_model->delete($id, $this->session->userdata('user_id'));
        echo json_encode(['status'=>'success']);
    }
}
