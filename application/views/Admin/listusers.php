<div class="span8">
    <h4>Examinees List</h4>
    <div style="
         height:300px;
         overflow:auto;
         ">
        <div class="table table-striped">
      
<!--              <?php echo var_dump($users); ?> -->
            <div id='dtable'></div>
            
        </div>
    </div>
</div>

<script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
      google.load('visualization', '1', {packages:['table']});
      google.setOnLoadCallback(drawTable);
      function drawTable() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Pin');
        data.addColumn('string', 'Date/Time Created');
        data.addColumn('string', 'Examinee Name');
        data.addColumn('string', 'Status');
        
       <?php
        
        foreach($users as $row){
             echo 'data.addRow(["'.$row["Pin"].'","'.$row["Created"].'","'.$row["UserName"].'","'.$row["Status"].'"]);';
             
        }
       ?>
		
/*         data.addRows([
          ['Mike',  'true', 'true','true'],
          ['Jim',   'true', 'true','true'],
          ['Alice', 'true', 'true','true'],
          ['Bob',  'true', 'true','true'],
        ]); */

        var table = new google.visualization.Table(document.getElementById('dtable'));
        table.draw(data, {allowHtml:true, page:'enable', pageSize:5});
      }
    </script>