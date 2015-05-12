<?php

class Exam extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('table');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('pagination');
        $this->load->model('exammodel');
        $this->load->library('pagination');
        $this->testforexaminee();
    }

    function index() {
        // test to make sure we have an examinee
        $this->testforexaminee();
        $this->intro();
    }

    function intro() {
        //set the page title
        $hdata['pagetitle'] = "Exam Introduction";
        $hdata['username'] = $this->session->userdata('username');


        $this->load->view('Site/header', $hdata);
        $this->load->view('Examinee/intro');
        $this->load->view('Site/footer');

        if ($this->input->post("submit")) {
            $this->examprep();
        }
    }

    function examprep() {
        //sets up exam for examinee
        //test whether there is an output record for this pin and get question sequence
        $pin = $this->session->userdata('pin');
        $seq = $this->exammodel->test_for_output_record($pin);
        //echo $seq;
        //test for reset startdate
        if ($seq != NULL) {

            //test for exam completed    
            if ($this->exammodel->test_for_finished($pin) == 1) {
                $this->logger->log($pin, 'Denied Exam because exam already completed');
                redirect('exam/complete');
            }
            //if exam has been started before and not reset by admin then redirect to incomplete
            if ($this->exammodel->test_for_started($pin) == 1) {
                $this->logger->log($pin, 'Denied Exam because exam incomplete and not reset');
                redirect('exam/incomplete');
                // if admin reset examinee startdate to null then calculate new exam time and set as session variable
            } else {
                $this->db->where('Pin', $this->session->userdata('pin'));
                $this->db->select('Elapsed');
                $query = $this->db->get('Results');
                $row = $query->row_array();
                //calculate time left in seconds
                $startelapsed = $row['Elapsed'];
                $examtime = 3600;
                $this->session->set_userdata('examtime', $examtime);
                $this->session->set_userdata('startelapsed', $startelapsed);
                $this->logger->log($pin, 'Exam started because user was reset');
            }
        } else {
            // if no output record create one and get question sequence
            $this->exammodel->create_output($this->session->userdata('pin'));
            $seq = $this->exammodel->test_for_output_record($this->session->userdata('pin'));
            $examtime = 3600;
            $this->session->set_userdata('examtime', $examtime);
            $this->logger->log($pin, 'Exam started first attempt');
        }




        //set exam question sequence as session variable
        $this->session->set_userdata('scoreemail', FALSE);
        $this->session->set_userdata('seq', $seq);
        $this->exammodel->startexamclock($this->session->userdata('pin'));

        redirect('exam/myexam');
    }

    function incomplete() {
        $hdata['pagetitle'] = "Incomplete";
        $hdata['username'] = $this->session->userdata('username');
        ;
        $this->load->view('Site/header', $hdata);
        $this->load->view('Examinee/incomplete');
        $this->load->view('Site/footer');
    }


    function myexam() {

        // get exam sequence
        $seq = $this->session->userdata('seq');
        //echo $seq;
        // convert question sequence to an array
        $seqarray = explode(',', $seq);

        // application currently only supports 1 question per page more work is needed to make it support multiple questions per page.
        $perpage = 1;

        //set page to uri segement and default to 0 if none specified
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $output = array_slice($seqarray, $page, $perpage, TRUE);
        //echo var_dump($output);

        $data['ques'] = $this->getques($output);
        //echo var_dump($data['ques']);
        $data['seqarray'] = $seqarray;
        $data['pin'] = $this->session->userdata('pin');


        //set the page title
        $hdata['pagetitle'] = "Exam";
        $hdata['username'] = $this->session->userdata('username');
        $hdata['time'] = $this->exammodel->calctimeleft($this->session->userdata('pin'));
        if ($data['ques']['qnum'] == 60) {
            $hdata['finishbutton'] = "<form action='" . site_url("exam/endexam") . "' method='post'><button class='btn btn-danger' type='submit' id='finishbutton'>Save Exam and Exit</button></form>";
        } else {
            $hdata['finishbutton'] = '';
        }

        $this->load->view('Site/examheader', $hdata);
        $this->load->view('Examinee/exam', $data);
        $this->load->view('Site/footer');
    }

    function getques($output) {
        //iterate through the sequence array and retrieve questions into a question array
        // get the question text and responses for each question id
        //$x = 0;
        foreach ($output as $output_key => $value) {
            //echo var_dump($output);
            $currques = $this->exammodel->get_exam_question($value);

            // build an array for each question iD
//            $ques[$x]['qid'] = $currques['Qid'];
//            $ques[$x]['qnum'] = key($output)+1;
//            $ques[$x]['qtext'] = $currques['Qtext'];
            $ques['qid'] = $currques['Qid'];
            $ques['qnum'] = key($output) + 1;
            $ques['qtext'] = $currques['Qtext'];

            //pull answer for this examinee and question from results table
            $answer = $this->exammodel->get_answer($this->session->userdata('pin'), $currques['Qid']);

            //build responses and indicated checked if already answered
            for ($y = 1; $y < 6; $y++) {
                if (chr(64 + $y) == $answer) {
                    $ques['R' . $y] =
                            '<label><td><input type="radio" name="answer" class="btn" checked="checked" value="' . chr(64 + $y) . '"></td><td><strong>' . chr(64 + $y) . ".&nbsp</strong></td><td>" . $currques["R" . $y] . '</td></label>';
                } elseif ($currques['R' . $y] != NULL) {

                    $ques['R' . $y] =
                            '<label><td><input type="radio" name="answer" class="btn" value="' . chr(64 + $y) . '"></td><td><strong>' . chr(64 + $y) . ".&nbsp</strong></td><td>" . $currques["R" . $y] . '</td></label>';
                } else {
                    $ques['R' . $y] = "";
                }
            }
            //$x++;
        }
        //echo var_dump($ques);
        return $ques;
    }

    function postques() {
        $seg = $this->input->post('segment');
        $pin = $this->session->userdata('pin');
        $qid = $this->input->post('qid');
        $value = $this->input->post('answer');

        $this->exammodel->post_answer($pin, $qid, $value);

        if ($this->input->post('forward')) {
            if ($seg < 59) {
                $goseg = $seg + 1;
            } else {
                $goseg = 0;
            }
        } elseif ($this->input->post('back')) {
            if ($seg > 0) {
                $goseg = $seg - 1;
            } else {
                $goseg = 59;
            }
        } else {
            $goseg = $seg;
        }
        redirect('/exam/myexam/' . $goseg);
    }

    function testforexaminee() {
        if ($this->session->userdata('role') != 'Examinee') {
            $this->session->sess_destroy();
            redirect('/login/login', 'refresh');
        }
    }

    function calculatetimeleft() {
        $secsleft = $this->exammodel->calctimeleft($this->session->userdata('pin'));
        return $secsleft;
    }

    function endexam() {
        $this->exammodel->exam_end($this->session->userdata('pin'));
		//$this->complete();
    }

}

/* End of file exam.php */