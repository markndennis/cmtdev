<div class="span6">
    <h4>Create New Examinee</h4>
    <div class="form-horizontal">
        <?php $attributes = $attributes = array('class' => 'form-horizontal', 'id' => 'createuser');
    echo form_open('admin/createuser', $attributes);
    ?>

<!--        <form method="post">-->

            <div class="control-group">
                <label class="control-label" for="pin">PIN:</label>
                <div class="controls">
                    <input type="text"id="pin" name="pin" value="<?php echo set_value('pin'); ?>" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="username">Examinee Name:</label>
                <div class="controls">
                    <input type="text" id="username" name="username" value="<?php echo set_value('username'); ?>" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="email">Email:</label>
                <div class="controls">
                    <input type="text" id="email" name="email" value="<?php echo set_value('email'); ?>" />
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    <input type="submit" class="btn" name="submit" value="Create Examinee" />
                </div>
            </div> 


        </form>
        <div style="color: red">
            <?php echo validation_errors(); ?>
        </div>

    </div>
</div>
<div class="span3">
    <?php 
    $this->table->set_heading(array('Last 10 Assigned PIN\'s'));
    $tmpl = array ( 'table_open'  => '<table class="table">' );
    $this->table->set_template($tmpl);

    echo $this->table->generate($lastpins);
    ;
    ?>
    
</div>