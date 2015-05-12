<div class="span8">
    <h4>Examinees List</h4>
    <div style="
         height:300px;
         overflow:auto;
         ">
        <div class="table table-striped">
      
             <?php //echo var_dump($users); ?>
            <div id="table_div"></div>
            
            
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
			$uname=addcslashes($row['UserName'],"'");
			//echo "data.addRow(['".$row['Pin']."','".$row['Created']."','".$row['UserName']."','".$row['Status']."'])\n";  
			echo "data.addRow(['".$row['Pin']."','".$row['Created']."','".$uname."','".$row['Status']."'])\n";
        }
       ?>
//        data.addRows([
//          ['Mike',  {v: 10000, f: '$10,000'}, true],
//          ['Jim',   {v:8000,   f: '$8,000'},  false],
//          ['Alice', {v: 12500, f: '$12,500'}, true],
//          ['Bob',   {v: 7000,  f: '$7,000'},  true]
//        ]);

        var table = new google.visualization.Table(document.getElementById('table_div'));
        table.draw(data, {allowHtml:true, page:'enable', pageSize:5});
      }
    </script>