    <div class="span7">
        <h4>Edit Existing Examinee</h4>
        <div class="form-horizontal">
<!--            <form class="form-horizontal" method="post">-->
                <?php $attributes = $attributes = array('class' => 'form-horizontal', 'id' => 'edituser');
    echo form_open('admin/edituser', $attributes);
    ?>

                <div class="control-group">
                    <label class="control-label" for="pin">PIN:</label>
                    <div class="controls">
                        <input type="text" name='pin' id='pin' readonly="readonly" value="<?php echo $user['Pin']; ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="statted">Started:</label>
                    <div class="controls">
                        <input type="text" name='started' id='started' readonly="readonly" value="<?php echo $user['Started']; ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="finished">Elapsed:</label>
                    <div class="controls">
                        <input type="text" name='finished' id='finished' readonly="readonly" value="<?php echo gmdate("H:i:s",$user['Elapsed']); ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="finished">Finished:</label>
                    <div class="controls">
                        <input type="text" name='finished' id='finished' readonly="readonly" value="<?php echo $user['Finished']; ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="status">Status:</label>
                    <div class="controls">
                        <input type="text" name='status' id='status' readonly="readonly" value="<?php echo $user['Status']; ?>" />
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="status">Score:</label>
                    <div class="controls">
                        <input type="text" name='score' id='score' readonly="readonly" value="<?php echo $user['Score']; ?>" />
                    </div>
                </div>


                <div class="control-group">
                    <label class="control-label" for="username">User Name: </label>
                    <div class="controls">
                        <?php echo $username ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="Email">Email:</label>
                    <div class="controls">
                        <?php echo $email ?>
                    </div>
                </div>
                <?php
                //echo $user['Status'];
                if ($user['Status'] == 'Incomplete') {
                    echo '<div class="control-group">
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" name="resetuser" /> Reset Examinee Start
                    </label>
                </div>
            </div>';
                }
                ?>
                <div class="control-group">
                    <div class="controls">
                        <input type="submit" class="btn" name="submit" value="Update User" />
                    </div>
                    <br/>
                    <div style="color: red;background-color:lightgray; padding-left:5px;">
                        <?php echo validation_errors(); ?>
                    </div>

            </form>
        </div>

    </div>
</div>
<div class="span2">
    <a href="../index.php?/admin/results/<?php echo $user['Pin']; ?>">View Detailed Results</a>
</div>

