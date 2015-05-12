<div class="span9">

    <h4>Number of Questions Completed:</h4>
    <div class="progress">

        <?php
        //progress bar
        $completed = $this->exammodel->count_completed($pin);
        $percent = round($completed / count($seqarray) * 100, 0);
        ?>
        <div class="bar" style="width: <?php echo $percent . '%' ?>">
            <?php echo $completed; ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="span9">
        <div class="pull-right">
            <select id="quesjump" >
                <option value="0" id="jump" selected="selected">Jump to Question ...</option>
                <?php
                for ($x = 0; $x < 60; $x++) {
                    $answered = $this->exammodel->get_answer($pin, $seqarray[$x]);


                    if ($answered == NULL || $answered == '0') {
                        ?>
                        <option value="<?php echo $x; ?>"> <?php echo $x + 1; ?></option>
                    <?php } else {
                        ?>
                        <option style="background-color: #0E90D2; color: white;" value="<?php echo $x; ?>"> <?php echo $x + 1 . " - Answered"; ?> </option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>

        <form id="exam" name="exam" method="post" action="<?php echo site_url('exam/postques'); ?>">
            <?php
            $currques = $ques
            ?>
            <input type="hidden" name="segment" value="<?php echo $this->uri->segment(3); ?>" />
<!--            QID( temp to be hidden):&nbsp;<input type="text" readonly="readonly" name="qid" value="<?php echo $currques['qid']; ?>" />-->

            <input type="hidden" name="qid" value="<?php echo $currques['qid']; ?>" />

            <h3>
                Question <?php echo $currques['qnum']; ?>&nbsp; &nbsp;
                <input type="submit" name="back" value="back" class="btn btn-success" />
                <input type="submit" name="save" value="save" class="btn btn-success" />
                <input type="submit" name="forward" value="next" class="btn btn-success" />
            </h3>
            <hr/>
            <div style="font-size:125%">
                <?php echo $currques['qtext']; ?>
            </div>
            <div style="font-size:125%">
                <br/>
            <table class="table table-hover">
                <tr><?php echo $currques['R1']; ?></tr>
                <tr><?php echo $currques['R2']; ?></tr>
                <tr><?php echo $currques['R3']; ?></tr>
                <tr><?php echo $currques['R4']; ?></tr>
                <tr><?php echo $currques['R5']; ?></tr>
            </table>
            </div>

            <br/>


        </form>

        <div id="testhere"></div>
    </div>

    <script>
        $(document).ready(function(){
           
           $('#jump').attr('selected', 'selected');
       
            // this is for queston jummp select
            $('#quesjump').change(function(){
                document.exam.submit();
                var x=$('#quesjump').val(); 
                window.location.href = '../index.php?/exam/myexam/' + x;
      
            });
        
            // this is for definitions
            $('.tt').popover({placement:'top',trigger:'hover'});
            
            
            // this attempts to disable the back button
            if (window.history) {
                window.history.forward();
            }
            
            //allows selection of radio button from text
           $("tr").click(function() {
                //alert("hello");
                $(this).find('input').attr("checked","checked");
                //$.post('../exam/postques');
                }
             );
            
            
        });
    </script>