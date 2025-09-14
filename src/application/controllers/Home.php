<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    public function __construct() {
        parent::__construct();
        // load session already autoloaded
    }

    public function index() {
        // If logged in, show profile page 
        if ($this->session->userdata('user_id')) {
            $this->load->view('frontend/profile');
            return;
        }
        // Not logged — public landing page 
        $this->load->view('frontend/index');
    }
}
