<!DOCTYPE html>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="expires" CONTENT="0">

        <link href="<?php echo base_url('/assets/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" />
        <script src="//code.jquery.com/jquery-latest.js"></script>
        <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js'); ?>"></script>
        <title><?php echo $pagetitle; ?></title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="span10">
                    <a href="http://www.cmtbc.bc.ca" target="_blank"><img src="<?php echo base_url('/assets/bootstrap/img/cmtbcheader.png');?>"></a>
                </div>


                <div class="span2">


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

                </div>

            </div>
            <hr/>
            <div class="row"> 
                <div class="span3">
                    <br/><br/>

                    <div id='timer'class="well well-small"><h4>Time Remaining:</h4>
                        <div id="mins"> <?php echo floor($time / 60) . ' Mins'; ?></div>
                        <div id="secs"> <?php echo $time % 60 . ' Secs'; ?></div>

                        <input type="hidden" id="time" value="<?php echo $time; ?>" />
                    </div>
                    <div id="warning" style="visibility: hidden; background-color:red; color: white; text-align:center">LOW TIME WARNING</div>
                    &nbsp;   
                    <?php echo $finishbutton; ?>
                </div>




                <script>
                    $(document).ready(function() {


                        var t = 0;
                        var m = 0;
                        var s = 0;

                        t = parseInt($('#time').val())


                        //alert(url);
                        //alert(t);
                        setInterval(timedisplay, 1000);
                        setInterval(posttime, 10000);

                        //                        

                        function timedisplay() {
                            //alert('timer');
                            t = t - 1;
                            // $('#test').html(t);
                            m = Math.floor(t / 60);
                            s = t % 60;

                            if (m < 5) {
                                $('#warning').css('visibility', 'visible');
                            }

                            if (m <= 0 && s <= 0) {
                                m = 0;
                                s = 0;
                                location.reload();
                                //posttime()
                            }
                            ;

                            //alert('s is ' + s);
                            $('#mins').html(m + ' Mins');
                            $('#secs').html(s + ' Secs');
                            

                        }
                        ;

                        function posttime() {
                            deriveurl="<?php echo site_url("/exam/calculatetimeleft") ?>";
                            $.post(deriveurl);
                            //location.reload();

                        }
                        ;

                    });
                </script>


