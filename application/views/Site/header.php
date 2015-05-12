<!DOCTYPE html>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
        <link href="<?php echo base_url('/assets/bootstrap/css/bootstrap.css'); ?>" rel="stylesheet">
        <script src="//code.jquery.com/jquery-latest.js"></script>
        <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js'); ?>"></script>
        <title><?php echo $pagetitle; ?></title>
        
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="span10"
                
                    <a href="http://www.cmtbc.bc.ca" target="_blank"><img src="<?php echo base_url('/assets/bootstrap/img/cmtbcheader.png');?>" width="940"></a>
                </div>
<!--                <div class="span2">

                  
                        <?php
                        if ($username != NULL) {
                            echo 'Welcome:<br/><strong>' . $username . '</strong><br/>';
                        }
                        ?>
                    
                    <?php
                    if ($this->session->userdata('role') == 'Administrator') {
                        echo anchor('login/logout/', 'Logout');
                    }
                    ?>

                    <div class="muted">
                        <br/>
                        <h4><?php echo $pagetitle ?></h4>
                    </div>

                </div>-->

            </div>
            <hr/>
            <div class="row"> 
                <div class="span3">
                    <br/><br/><br/>
                </div>
                &nbsp;
         
        



