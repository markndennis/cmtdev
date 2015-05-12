<?php

class Usermodel extends CI_Model {

    function __construct() {
// Call the Model constructor
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('exammodel');
        $this->load->library('session');

    }

    function createuser($pin, $username, $email) {

        $role = 'Examinee';
        $datetime = date('Y-m-d H:i:s', time());

        $data = array(
            'Pin' => $pin,
            'Username' => $username,
            'Email' => $email,
            'Role' => $role,
            'Created' => $datetime
        );
        $this->db->insert('Users', $data);
//        $this->db->where('Pin', $pin);
//        $this->db->get('Users');
    }

    function updateuser($pin, $username, $email, $resetuser) {

        if ($resetuser === "on") {
            //echo 'pin is :' . $pin;
            $data = array(
                'Started' => NULL,
            );

            $this->db->where('Pin', $pin);
            $this->db->update('Results', $data);
            $this->logger->log($pin,'user reset for exam start by ' . $this->session->userdata['username']);
        }

        $data = array(
            'Username' => $username,
            'Email' => $email,
        );

        $this->db->where('Pin', $pin);
        $this->db->update('Users', $data);
        
        $this->db->where('Pin','test01');
        $this->db->get('Users');
        //echo $this->session->userdata['username'];
        $this->logger->log($pin, 'record changed by Administrator');
    }

    function retrieve_username_role($pin) {

        $this->db->where('Pin', $pin);
        $this->db->select('UserName,Role');
        $query = $this->db->get('Users');
        $row = $query->row_array();
        return $row;
    }

    function retrieve_user($pin) {
        $this->db->where('Users.Pin', $pin); // where Users.Pin == $pin
        $this->db->from('Users'); // run that query
        $this->db->join('Results', 'Results.Pin = Users.Pin','left');
        $query = $this->db->get();
        $row = $query->row_array();
        $row['Pin']=$pin;
        $row['Status'] = $this->exammodel->exam_status($row['Pin']);
        
        //echo var_dump($row);
        return $row;
    }

    function retrieve_examinees($field,$ord) {
        $this->db->where('Role', 'Examinee'); // where Role = Examninee
        $this->db->select('Users.Pin, Users.Created, Users.UserName'); //select columns
        $this->db->order_by("Users.".$field, $ord); //newest entry on top
        
        $this->db->from('Users'); // run that query
        
        //$this->db->join('Results', 'Results.Pin = Users.Pin','left');
        
        $query = $this->db->get();
        
        $data = $query->result_array();
        
// edit the array to convert Pin's to links to edit user
        $x = 0;
        foreach ($data as $row) {
            $newpin = "<a href= " . site_url('/admin/edituser/' . $row["Pin"]) . ">" . $row['Pin'] . "</a>";
            $data[$x]['Pin'] = $newpin;
            $data[$x]['Status'] = $this->exammodel->exam_status($row['Pin']);
            $x = $x + 1;
        }

        return $data;
    }

    function retrieve_examineesj() {

//        $this->db->where('Role', 'Examinee'); // where Role = Examninee
//        $this->db->select('Users.Pin, Users.Created, Users.UserName'); //select columns
//        $this->db->order_by("Users.Created", "desc"); //newest entry on top
        
        //$this->db->from('Users'); // run that query
        
        //$this->db->join('Results', 'Results.Pin = Users.Pin','left');
        $this->db->where('Role', 'Examinee'); // where Role = Examninee
        $query = $this->db->get('Users');
        
        $data = $query->result_array();
        
// edit the array to convert Pin's to links to edit user
        $x = 0;
        foreach ($data as $row) {
            $newpin = "<a href= " . site_url('/admin/edituser/' . $row["Pin"]) . ">" . $row['Pin'] . "</a>";
            $data[$x]['Pin'] = $newpin;
            $data[$x]['Status'] = $this->exammodel->exam_status($row['Pin']);
            $x = $x + 1;
        }
        // echo var_dump($data);
        return $data;
    }

    
    function getpins() {
        $this->db->select('Pin');
        $this->db->order_by("Created", "desc");
        $this->db->limit(10);
        $query = $this->db->get('Users');
        $lastpins = $query->result_array();
//echo var_dump($lastpins);
        return $lastpins;
    }
}
    /* End of file usermodel.php */