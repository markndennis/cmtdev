<?php

class Exammodel extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('logger');
        $this->load->model('mailer');
        //$this->load->library('email');
    }

    function generate_random_questions() {

        // count the total number of questions
        $num = $this->db->count_all('Questions');
        //echo $num;
        //create an array with number elements equal to question count note that question numbers must match question Qid
        for ($x = 1; $x < $num + 1; $x++) {
            $seq[] = $x;
        }

        // randomize the array
        shuffle($seq);

        //create a delimited string containing the randomized values
        $seqstr = implode(',', $seq);

        return $seqstr;
    }

    function get_exam_question($qid) {
        //returns a specific question from database
        $query = $this->db->get_where('Questions', array('Qid' => $qid));
        $ques = $query->row_array();

        return $ques;
    }

    function create_output($pin) {
        //creates new exam record and populates examinee, started and question sequence
        $seq = $this->generate_random_questions();
        //$datetime = date('Y-m-d H:i:s', time());

        $data = array(
            'Pin' => $pin,
            'Seq' => $seq
        );

        $this->db->insert('Results', $data);
        $this->logger->log($pin, 'Output record created and randomized questions logged');
    }

    function startexamclock($pin) {
        $datetime = date('Y-m-d H:i:s', time());
        //echo $datetime;
        $data = array(
            'Started' => $datetime);
        $this->db->where('Pin', $pin);
        $this->db->update('Results', $data);
    }

    function exam_end($pin) {
        // ends exam, posts finished time, score exam and trigger results email
        //get and post finished time
        $alreadydone = $this->test_for_finished($pin);
        
        if ($alreadydone == 0) {
            $datetime = date('Y-m-d H:i:s', time());
            $data = array(
                'Finished' => $datetime,);
            $this->db->where('Pin', $pin);
            $this->db->update('Results', $data);
            //score exam
            $correct = $this->score_exam($pin);
            //$results = $this->examresults($pin);
            
            $this->post_score($pin, $correct);
            $this->logger->log($pin, 'Exam end');
    
            //$to = 'onlinejuris@cmtbc.ca,info@cmtbc.ca,adam@veritagroup.com,markndendev@gmail.com';
            $to = 'markndendev@gmail.com';
            $subject = 'Examinee ' . $pin . ' has completed their Jurisprudence exam';
            $message = '<html>This is an automatically generated e-mail notification.<br/><hr/>
                <ul>
                    <li>Name: <strong>' . $this->session->userdata('username') . '</strong></li>
                    <li>Date: <strong>' . $datetime . '</strong></li>
                    <li>Score: <strong>' . $correct . '</strong></li>
                </ul><hr/>
                       </html>';
    
            $this->mailer->email_send($to, $subject, $message);
            $this->logger->log($pin, 'Score email sent to Admin');
           
    		$hdata['username'] = $this->session->userdata('username');
    		$this->session->sess_destroy();
            $hdata['pagetitle'] = "Complete";      
            $this->load->view('Site/header', $hdata);
            $this->load->view('Examinee/complete');
            $this->load->view('Site/footer');   
        }
        
    }


    function calctimeleft($pin) {
        //get start time
        //echo "calculatetimeleft called:";
        $this->db->where('Pin', $pin);
        $this->db->select('Started');
        $query = $this->db->get('Results');
        $row = $query->row_array();
        //calculate time left in seconds
        $started = strtotime($row['Started']);
        $elapsed = time() - $started + $this->session->userdata('startelapsed');
        $secsleft = $this->session->userdata('examtime') - $elapsed;
        //echo $secsleft;
        //post elapsed time
        
        if ($secsleft <= 0) {
            $this->logger->log($pin, 'Time Expired');
            $this->exam_end($pin);
        }

        $data = array(
            'Elapsed' => $elapsed);
        $this->db->where('Pin', $pin);
        $this->db->update('Results', $data);



        return $secsleft;
    }

    function test_for_output_record($pin) {
        //returns exam number sequence
        $output = $this->db->get_where('Results', array('Pin' => $pin));

        if ($output->num_rows() != 0) {
            $row = $output->row_array();
            $seq = $row['Seq'];
            //echo $seq;
            return $seq;
        } else {
            $seq = NULL;
            return $seq;
        }
    }

    function test_for_started($pin) {
        $this->db->where('Pin', $pin);
        $this->db->select('Started');
        $query = $this->db->get('Results');
        $row = $query->row_array();
        // tests for null in started
        if ($row['Started'] == NULL) {
            return 0;
        } else {
            return 1;
        }
    }

    function test_for_finished($pin) {
        $this->db->where('Pin', $pin);
        $this->db->select('Finished');
        $query = $this->db->get('Results');
        $row = $query->row_array();

        if ($row['Finished'] == NULL) {
            return 0;
        } else {
            return 1;
        }
    }

    function post_answer($pin, $qid, $value) {
        // posts the exam answer to results
        $data = array(
            "Q" . $qid => $value
        );
        $this->db->where('Pin', $pin);
        $this->db->update('Results', $data);
        //echo "posted I think";
    }

    function get_answer($pin, $qid) {
        // gets an exam answer for a examinee and question number
        $query = $this->db->get_where("Results", array('Pin' => $pin));
        $answer = $query->row_array();
        return $answer['Q' . $qid];
    }

    function count_completed($pin) {
        //counts number of questions with non null answers
        $query = $this->db->get_where("Results", array('Pin' => $pin));
        $result = $query->row_array();
        $count = 0;
        for ($x = 1; $x < 61; $x++) {
            $ques = "Q" . $x;
            if ($result[$ques] != NULL and $result[$ques] != '0') {
                $count = $count + 1;
            }
        }
        return $count;
    }

    function exam_status($pin) {
        //$status="hello from exammodel";
        $query = $this->db->get_where("Results", array('Pin' => $pin));
        $result = $query->row_array();


        if (count($result) == 0) {
            $status = "Not Started";
        } elseif ($result['Started'] == NULL && $result['Elapsed'] != NULL) {
            $status = "Reset";
        } elseif ($result['Started'] == NULL && $result['Elapsed'] == NULL) {
            $status = "Not Started";
        } elseif ($result['Finished'] != NULL) {
            $status = "Completed";
        } else {
            $status = "Incomplete";
        }
        //echo $status;
        return $status;
    }

    function get_solution($ques) {
        $query = $this->db->get_where("Questions", array('Qid' => $ques));
        $question = $query->row_array();
        //echo var_dump($question);
        $solution = $question['Solution'];
        echo "getsolution: " . $solution;
        return $solution;
    }

    function score_exam($pin) {
        // this method scores the exam by getting all the solutions and then comparing with the examinee answers
        $this->db->select('Qid,Solution');
        $query1 = $this->db->get('Questions');
        $allsolution = $query1->result_array();

        $query = $this->db->get_where("Results", array('Pin' => $pin));
        $result = $query->row_array();
        $correct = 0;

        for ($x = 1; $x < 61; $x++) {
            $answer = $result['Q' . $x];

            if ($answer == $allsolution[$x - 1]['Solution']) {
                $correct = $correct + 1;
            }
        }

        //$this->email_results($pin, $correct);
        return $correct;
    }

    function examresults($pin) {
        $this->db->where('Pin', $pin);
        $result = $this->db->get('Results');
        $query = $result->result_array();
        $dispques = explode(",", $query[0]['Seq']);

        for ($x = 1; $x < 61; $x++) {
            $soln = $this->exammodel->examsolution($x);
            if ($query[0]['Q' . $x] === $soln) {
                $corr = " - <span style='color:red;'><strong>Correct !</strong></span>";
            } else {
                $corr = "";
            }

            if ($query[0]['Q' . $x] === NULL) {
                $query[0]['Q' . $x] = "NULL";
            }
            $disporder = array_search($x, $dispques) + 1;

            $query[0]['Q' . $x] = "(" . $disporder . ") answer =" . $query[0]['Q' . $x] . " ( soln = " . $soln . ")" . $corr;
            //echo $query[0]['Q' . $x];
        }

        return $query;
    }

    function examsolution($qnum) {
        $this->db->select('Solution');
        $this->db->where('Qid', $qnum);
        $query = $this->db->get('Questions');
        $result = $query->result_array();
        $soln = $result[0]['Solution'];
        return $soln;
    }

    function examquestions($pin, $qnum) {
        $this->db->where('Pin', $pin);
        $result = $this->db->get('Results');
        $query = $result->result_array();

        //var_dump($query);

        $seq = explode(",", $query[0]['Seq']);

        //echo var_dump($seq);

        $result = $this->db->get('Questions');
        $ques = $result->result_array();
        //echo var_dump($ques);
        $x = 0;
        $exam = array();
        foreach ($seq as $quesnum) {
            //echo $quesnum;
            $exam[$x]['qnum'] = ($x + 1);
            $exam[$x]['qtext'] = $ques[$quesnum - 1]['Qtext'];
            $exam[$x]['qr1'] = $ques[$quesnum - 1]['R1'];
            $exam[$x]['qr2'] = $ques[$quesnum - 1]['R2'];
            $exam[$x]['qr3'] = $ques[$quesnum - 1]['R3'];
            $exam[$x]['qr4'] = $ques[$quesnum - 1]['R4'];
            $exam[$x]['qr5'] = $ques[$quesnum - 1]['R5'];
            $x++;
        }
        //echo var_dump($exam);
        return $exam;
    }

    function post_score($pin, $correct) {
        $data = array(
            "Score" => $correct
        );
        $this->db->where('Pin', $pin);
        $this->db->update('Results', $data);
    }

    // no longer used
    /*
	function email_results($pin, $correct) {
        if (!$this->session->userdata('scoreemail')) {
            // Email account and to information
            $ACCOUNT = 'admin@markndennis.com';
            $PASSWORD = 'public01';
            //$EMAILTO = 'marknden@gmail.com';
            $EMAILTO = 'markndendev@gmail.com';
            $MESSAGE = '<html>Examinee <strong>' . $pin . '</strong> has completed their exam.<br/>  The number of correct answers was <strong>' . $correct . '</strong>. <br/>This information has been logged and can be accessed from the List Examinees page.</html>';
            $SUBJECT = 'Examinee ' . $pin . ' has completed their exam';

            // Email configuraion
            $config['mailpath'] = "/usr/sbin/sendmail";
            $config['protocol'] = "sendmail";
            $config['smtp_host'] = "relay-hosting.secureserver.net";
            $config['smtp_user'] = $ACCOUNT;
            $config['smtp_pass'] = $PASSWORD;
            $config['smtp_port'] = "25";
            $config['mailtype'] = "html";
            $config['wordwrap'] = "TRUE";
            //$config['validate'] = "TRUE";

            $this->load->library('email', $config);
            $this->email->set_newline("\r\n");

            $this->email->from($ACCOUNT);
            $this->email->to($EMAILTO);
            //$this->email->cc('another@another-example.com');
            //$this->email->bcc('them@their-example.com');

            $this->email->subject($SUBJECT);
            $this->email->message($MESSAGE);

            $this->email->send();
            $this->logger->log($pin, 'Email with score sent to admin');
            $this->session->set_userdata('scoreemail', TRUE);

            //echo $this->email->print_debugger();
        }
    }
    */
}

/* End of file exammodel.php */
