<?php

session_start();

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        //$this->load->database();
        $this->load->library('table');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('usermodel');
        $this->load->model('logger');
        // test to ensure we have an administrator and if not send to login
        $this->testforadmin();
        $hdata['username'] = $this->session->userdata('username');
    }

    function index() {
        $this->testforadmin();
        $this->listusers();
    }

    function listusers() {
        //get a list of all examinees$ord = $this->uri->segment(4, 'desc');
        $data['users'] = $this->usermodel->retrieve_examineesj();
		//echo var_dump($data['users']);


        //set the page title
        $hdata['pagetitle'] = "List Users";
        $hdata['username'] = $this->session->userdata('username');

        $this->load->view('Site/adminheader', $hdata);
        $this->load->view('Admin/listusers', $data);
        $this->load->view('Site/footer');
    }
    
    function results($pin){
        $hdata['pagetitle'] = "List Users";
        $hdata['username'] = $this->session->userdata('username');

        $result = $this->exammodel->examresults($pin);
	
        $data['results'] = $result;
        
        
        $this->load->view('Site/adminheader', $hdata);
        $this->load->view('Admin/results', $data);
        $this->load->view('Site/footer');
    }
    
    function listusersj($field="Created") {
        //get a list of all examinees
        $ord = $this->uri->segment(4, 'desc');
        $data['users'] = $this->usermodel->retrieve_examinees($field,$ord);


        //set the page title
        $hdata['pagetitle'] = "List Users";
        $hdata['username'] = $this->session->userdata('username');

        $this->load->view('Site/adminheader', $hdata);
        $this->load->view('Admin/listusersj', $data);
        $this->load->view('Site/footer');
        
    }
    

    function createuser() {
        
        //set the page title
        $hdata['pagetitle'] = "Create User";
        $hdata['username'] = $this->session->userdata('username');

        // set form validation criteria
        $this->form_validation->set_rules('pin', 'Pin', 'trim|required|min_length[4]|max_length[12]|is_unique[Users.Pin]');
        $this->form_validation->set_rules('username', 'UserName', 'trim|required|min_length[4]|max_length[40]');
        //$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[Users.Email]'); // unique validation will cause problems with user reset.
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        //display and validate the form
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('Site/adminheader', $hdata);
            $data['message'] = "<div class='alert'><button type='button' class='close' data-dismiss='alert'>×</button>" . validation_errors() . "</div>";
            $data['lastpins']=$this->usermodel->getpins();
            $this->load->view('Admin/createuser', $data);
            $this->load->view('Site/footer');
        } else {
            // get validated form values
            $pin = $this->input->post('pin');
            $username = $this->input->post('username');
            $email = $this->input->post('email');

            // post user to users table
            $this->usermodel->createuser($pin, $username, $email);
            
            // email user info
            $message="<html><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>Thank you for signing up for the Bylaw Workshop Online Evaluation.  You will need a PIN in order to log in.<br><br>Your PIN is <strong>" . $pin . "</strong> .<br><br>                
You can log into your evaluation at http://www.exam.cmtbc.ca/exam2/ .<br><br>
If you have any questions please contact the college.<br><br>
<small>College of Massage Therapists of British Columbia
#304-1212 West Broadway Vancouver, BC V6H 3V1<br>
Tel: (604) 736-3404 Toll free in B.C. 1-877-321-3404<br>
Fax: (604) 736-6500 www.cmtbc.bc.ca<br><br> 
The CMTBC serves and protects the public by regulating the profession of massage therapy in B.C. in accordance with the Health Professions Act. We believe in personal integrity, administrative fairness and professional accountability.</small></html>";
            $this->mailer->email_send($email,"Your CMTBC Bylaw Workshop Evaluation PIN for " . $username,$message);

          
            redirect('admin/listusers');
        }
    }

    // DEVELOPMENT TESTING REMOVE 
    function removetest() {
        $this->db->where('Role', 'Examinee');
        $this->db->delete('Users');
        $this->db->empty_table('Results');
        $this->listusers();
    }

    // DEVELOPMENT TESTING REMOVE
    function createtest() {
        for ($x = 1; $x < 11; $x++) {

            if ($x < 10) {
                $num = '0' . (string) $x;
            } else {
                $num = (string) $x;
            }
            $pin = 'test' . $num;
            $username = $pin;
            $role = 'Examinee';
            $email = $username . '@' . $username . '.com';
            $datetime = date('Y-m-d H:i:s', time());
            $data = array(
                'Pin' => $pin,
                'Username' => $username,
                'Email' => $email,
                'Role' => $role,
                'Created' => $datetime
            );
            $this->db->insert('Users', $data);
        }
        $this->listusers();
    }


    function deleteexamdata() {
        $this->db->empty_table('Results');
        $this->listusers();
    }

    function edituser() {
        $this->testforadmin();
        $pin = $this->uri->segment(3);

        $data['user'] = $this->usermodel->retrieve_user($pin);
        
        // set form fields
        $data['username'] = form_input('username', $data['user']['UserName']);
        $data['email'] = form_input('email', $data['user']['Email']);

        // define validation criteria
        $this->form_validation->set_rules('username', 'UserName', 'trim|required|min_length[4]|max_length[40]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        // set page title and username
        $hdata['pagetitle'] = "Edit User";
        $hdata['username'] = $this->session->userdata('username');

        //test for submitted validated form
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('Site/adminheader', $hdata);
            $this->load->view('Admin/edituser', $data);
            $this->load->view('Site/footer');
        } else {
            $pin = $this->input->post('pin');
            $email = $this->input->post('email');
            $username = $this->input->post('username');
            $resetuser = $this->input->post('resetuser');
            $this->usermodel->updateuser($pin, $username, $email, $resetuser);
            redirect('admin/listusers/', 'refresh');
        }
    }

    function testforadmin() {
        if ($this->session->userdata('role') != 'Administrator') {
            $this->session->sess_destroy();
            redirect('/login/login', 'refresh');
        }
    }
    
    function listlog(){
     
        $hdata['pagetitle'] = "List Log";
        $hdata['username'] = $this->session->userdata('username');
        
        $data['log']=$this->logger->getlog();
        
        
        
        $this->load->view('Site/adminheader', $hdata);
        $this->load->view('Admin/showlog', $data);
        $this->load->view('Site/footer');
        
        if ($this->input->post('submit')){
            //echo "you hit submit";
            $this->logger->log2csv();
            redirect('admin/listlog');    
        }
    }

}

/* End of file admin.php */