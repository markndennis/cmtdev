<div class="span9">
    <h4>Log File</h4>
    <div style="
         height:300px;
         overflow:auto;
         ">
        <div class="table table-striped">
            <?php
            $this->table->set_heading(array('Pin', 'Message', 'Date/Time'));
            echo $this->table->generate($log);
            ?>
        </div>
    </div>
    <form method="post">
        <input type="submit"class="btn btn-danger" name="submit" value="save and clear log" />
    </form>
</div>



<!--<script>
$(document).ready(function() {
   $(".alert").alert('close')
});
            

</script>-->