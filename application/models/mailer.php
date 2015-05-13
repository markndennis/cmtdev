<?php
class Mailer extends CI_Model {
    

function email_send($to,$subject,$message, $attachment='') {

        // Email account and to information
        //$ACCOUNT = 'onlinejuris@veritagroup.com';
        //$PASSWORD = 'HZIz5FA12GCRjKqMLKSN';   
        $prefix = 'marknden';
        $suffix = 'ety01';
        $ACCOUNT = $prefix . 'dev@gmail.com';
        $PASSWORD = 'Saf' . $suffix;    

        // Email configuration
        //$config['mailpath'] = "/usr/sbin/sendmail";
        $config['protocol'] = "smtp";
        //$config['smtp_host'] = "mail.veritagroup.com";
        $config['smtp_host'] = "ssl://smtp.gmail.com";
        $config['smtp_user'] = $ACCOUNT;
        $config['smtp_pass'] = $PASSWORD;
        $config['smtp_port'] = "465";
        $config['mailtype'] = "html";
        $config['wordwrap'] = "TRUE";
        $config['charset'] = "utf-8";
        //$config['validate'] = "TRUE";
        
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->set_crlf( "\r\n" );

        $this->email->from($ACCOUNT);
        $this->email->to($to);
        //$this->email->cc('adam@veritagroup.com,marknden@gmail.com');
        $this->email->bcc('markndendev@gmail.com');

        $this->email->subject($subject);
        $this->email->message($message);
        
        if ($attachment != ''){
            $this->email->attach($attachment);
        }
        
        $this->email->send();
        $this->logger->log($this->session->userdata('username'),'Email with subject '.$subject.' sent');
        //$mailresult = $this->email->print_debugger();
        //$this->logger->log($this->session->userdata('username'),$mailresult);
        

        //echo $this->email->print_debugger();
    }
}
?>
