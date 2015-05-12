<div class="span6">
    <h4>Examinees List</h4>
    <div style="
         height:300px;
         overflow:auto;
         ">
        <div class="table table-striped">
            <?php
            //$this->table->set_heading(array('<a href="http://markndennis.com/CMTOJE/index.php?/admin/listusers/Pin/asc">Pin</a>', '<a href="http://markndennis.com/CMTOJE/index.php?/admin/listusers/Created/desc"> Date/Time Created</a>', '<a href="http://markndennis.com/CMTOJE/index.php?/admin/listusers/UserName/asc">Examinee Name</a>', 'Status'));
            $this->table->set_heading(array('<a href="index.php?/admin/listusers/Pin/asc">Pin</a>', '<a href="index.php?/admin/listusers/Created/desc"> Date/Time Created</a>', '<a href="index.php?/admin/listusers/UserName/asc">Examinee Name</a>', 'Status'));

            echo $this->table->generate($users);
            ?>
        </div>
    </div>
</div>

<!--            <?php //echo anchor('/admin/removetest/', 'Delete User Test Data');  ?><br/>
<?php //echo anchor('/admin/createtest', 'Create User Test Data'); ?><br/>-->


<!--<script>
$(document).ready(function() {
   $(".alert").alert('close')
});
            

</script>-->