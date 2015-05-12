<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start();

class Login extends CI_Controller{
    
 public function __construct() {
        parent::__construct();
        //$this->load->database();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('user_agent');
        $this->load->model('usermodel');
        $this->load->model('logger');
    }
    
     public function index() {
//$this->load->view('welcome_message');
        $this->login();
    }

    function login() {

        $this->session->sess_destroy();
        // set header page parameters
        $hdata['pagetitle'] = "";
        $hdata['username'] = "";
        
        
        $userdata = array('role'=>'guest');
        $this->session->set_userdata($userdata);

        // call views
        $this->load->view('Site/header', $hdata);
        $this->load->view('login');
        $this->load->view('Site/footer');

        // if postback test for valid pin
        if ($this->input->post('submit')) {
       

            //get login form pin
            $pin = $this->input->post('pin');

            //get UserName and Role for pin
            $query = $this->usermodel->retrieve_username_role($pin);
            //echo var_dump($query);

            if ($query != NULL) {

                $userdata = array(
                    'pin' => $pin,
                    'username' => $query['UserName'],
                    'role' => $query['Role']
                );

                $this->session->set_userdata($userdata);         
            }

            if ($this->session->userdata('role') == 'Administrator') {
                //log user
                $this->logger->log($pin,'Administrator Logged In');
                $message=$this->agent->agent_string();
                $this->logger->log($pin,$message);
                redirect('/admin/listusers/','refresh');
            } elseif ($this->session->userdata('role') == 'Examinee') {
                //log user
                $this->logger->log($pin,'Examinee Logged In');
                $message=$this->agent->agent_string();
                $this->logger->log($pin,$message);
                redirect('/exam/intro/', 'refresh');
            } else {
                redirect('login/login', 'refresh');
            }
        }
    }

    function logout() {
        session_destroy();
        $this->logger->log($this->session->userdata('username'),'Logged out');
        redirect('login/login', 'refresh');
    }
    
    function test(){
        $hdata['pagetitle'] = "test";
        $hdata['username'] = "";
        $this->load->view('Site/header', $hdata);
        $this->load->view('test');
        $this->load->view('Site/footer');
    }
}

/* End of file */
