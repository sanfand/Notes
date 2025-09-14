<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->database();
        $this->load->model('User_model');
        $this->load->helper(array('url', 'form'));
    }

    // --- Show login page ---
    public function login() {
        if ($this->session->userdata('user_id')) {
            redirect('notes/profile');
        }
        $this->load->view('frontend/login');
    }

    // --- Handle login via AJAX ---
    public function do_login() {
        $username = $this->input->post('username');
        $password = md5($this->input->post('password'));

        $user = $this->User_model->get_by_credentials($username, $password);

        if ($user) {
            $this->session->set_userdata('user_id', $user->id);
            $this->session->set_userdata('username', $user->username);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
        }
    }

    // --- Show register page ---
    public function register() {
        if ($this->session->userdata('user_id')) {
            redirect('notes/profile');
        }
        $this->load->view('frontend/register');
    }

    // --- Handle register via AJAX ---
    public function do_register() {
        $username = $this->input->post('username');
        $email    = $this->input->post('email');
        $password = md5($this->input->post('password'));

        $data = [
            'username' => $username,
            'email'    => $email,
            'password' => $password
        ];

        if ($this->User_model->create($data)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Register failed']);
        }
    }

    // --- Logout ---
    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
