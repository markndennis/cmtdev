<?php

class Logger extends CI_Model {

    function __construct() {

//        // Call the Model constructor
//        parent::__construct();
//        //$this->load->database();
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->model('mailer');
//        //$this->load->library('email');
    }

    function log($pin, $message) {
        $datetime = date('Y-m-d H:i:s', time());
        $data = array(
            'Pin' => $pin,
            'Message' => $message,
            'Date' => $datetime
        );
        $this->db->insert('Log', $data);
    }

    function getlog() {

        $this->db->select('Pin,Message,Date'); //select columns
        $this->db->order_by("Date", "desc"); //newest entry on top

        $this->db->from('Log'); // run that query
        //$this->db->join('Results', 'Results.Pin = Users.Pin','left');

        $query = $this->db->get();

        $data = $query->result_array();

        return $data;
    }

    function log2csv() {
        //echo 'log2csv called';
        $this->db->select('Event_Id,Pin,Message,Date'); //select columns
        $this->db->order_by("Date", "desc"); //newest entry on top

        $query = $this->db->get('Log');

        //$data = $query->result_array();
        $delimiter = ",";
        $newline = "\r\n";

        $data = $this->dbutil->csv_from_result($query, $delimiter, $newline);
        $datetime = date('Ymd', time());
        $filename = 'log' . $datetime;
        if (!write_file('./assets/files/' . $filename . '.csv', $data,'wb')) {
            $this->logger->log('Administrator', 'Unable to save log file');
        } else {
            $this->db->empty_table('Log');
            $this->logger->log('Administrator', 'Log File Saved');
            $this->logger->log('Administrator', 'Log File Emptied');
            $this->logger->emaillog('./assets/files/' . $filename . '.csv');
        }
                    
        //return $data;
    }
    
    function emaillog($attachment){
        $to = 'onlinejuris@cmtbc.ca';
        $subject = 'Archived Log File';
        $message = '<html>This is an automatically generated e-mail notification.<br/>Please find attached a copy of the recently archived log file.<br/><br/></html>';
        
        $this->mailer->email_send($to, $subject, $message, $attachment);
        
    }

}

/* End of file logger.php */