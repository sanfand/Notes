<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('session');
        $this->load->helper(['url', 'form']);
    }

    public function login() {
        if ($this->input->method() === 'post') {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $username = $username ? htmlspecialchars($username, ENT_QUOTES, 'UTF-8') : '';
            $password = $password ? htmlspecialchars($password, ENT_QUOTES, 'UTF-8') : '';

            if (empty($username) || empty($password)) {
                $this->session->set_flashdata('error', 'Username and password are required.');
                redirect('auth/login');
                return;
            }

            $user = $this->User_model->get_user_by_username($username);

            if ($user && password_verify($password, $user->password)) {
                $this->session->set_userdata([
                    'user_id'   => $user->id,
                    'username'  => $user->username,
                    
                ]);
                redirect('profile');
            } else {
                $this->session->set_flashdata('error', 'Invalid username or password.');
                redirect('auth/login');
            }
        } else {
            $this->load->view('login'); 
        }
    }

    public function register() {
        if ($this->input->method() === 'post') {
            $username = $this->input->post('username');
            $email    = $this->input->post('email');
            $password = $this->input->post('password');

            if ($this->User_model->username_exists($username)) {
                $this->session->set_flashdata('error', 'Username already exists.');
                redirect('auth/register');
                return;
            }

            if ($this->User_model->email_exists($email)) {
                $this->session->set_flashdata('error', 'Email already exists.');
                redirect('auth/register');
                return;
            }

            $hash = password_hash($password, PASSWORD_BCRYPT);

            $this->User_model->insert_user([
                'username' => $username,
                'email'    => $email,
                'password' => $hash
            ]);

            $this->session->set_flashdata('success', 'Registration successful. You can login now.');
            redirect('auth/login');
        } else {
            $this->load->view('register'); 
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
