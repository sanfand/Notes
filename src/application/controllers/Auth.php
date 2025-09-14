<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        // session & db autoloaded via autoload.php
        $this->load->helper('url');
    }

    public function index() {
        if ($this->session->userdata('user_id')) redirect('profile');
        redirect('login');
    }

    // show login view
    public function login() {
        if ($this->session->userdata('user_id')) redirect('profile');
        $this->load->view('login');
    }

    // AJAX: perform login
    public function do_login() {
        if ($this->input->method() !== 'post') {
            return $this->output->set_status_header(405);
        }

        // var_dump(password_hash('1234f', PASSWORD_DEFAULT));
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);

        $user = $this->User_model->get_by_username_or_email($username);
        // var_dump(password_verify($password, $user->password));

        $response = ['status'=>'error','message'=>'Invalid username/email or password.'];

        if ($user) {
            if (password_verify($password, $user->password)) {
                $this->session->set_userdata([
                    'user_id'   => $user->id,
                    'username'  => $user->username,
                    'email'     => $user->email,
                ]);
                $response = ['status'=>'success','message'=>'Login successful'];
            } else {
                // optional fallback for legacy MD5 users:
                // if ($user->password === md5($password)) {
                //     $this->User_model->update_password($user->id, password_hash($password, PASSWORD_DEFAULT));
                //     // set session...
                // }
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    // show register view
    public function register() {
        if ($this->session->userdata('user_id')) redirect('profile');
        $this->load->view('register');
    }

    // AJAX: perform register
    public function do_register() {
        if ($this->input->method() !== 'post') {
            return $this->output->set_status_header(405);
        }

        $username = $this->input->post('username', TRUE);
        $email    = $this->input->post('email', TRUE);
        $password = $this->input->post('password', TRUE);

        // Basic validation 
        if (!$username || !$email || !$password) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status'=>'error','message'=>'Please fill all fields']));
        }

        // prevent duplicates
        if ($this->User_model->get_by_username_or_email($username) || $this->User_model->get_by_username_or_email($email)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status'=>'error','message'=>'Username or email already exists']));
        }

        $data = [
            'username'   => $username,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
        ];

        $insert_id = $this->User_model->insert($data);

        if ($insert_id) {
            $response = ['status'=>'success','message'=>'Registration successful'];
        } else {
            $response = ['status'=>'error','message'=>'Registration failed'];
        }

        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }
}
