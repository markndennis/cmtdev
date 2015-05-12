<div class="span7">
    
    <table class="table">
        <col style="width:20%">
        <col style="width:80%">
        <thead>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Pin</td><td><?php echo $results[0]['Pin']; ?></td></tr>
            <tr><td>Started</td><td><?php echo $results[0]['Started']; ?></td></tr>
            <tr><td>Elapsed</td><td><?php echo $results[0]['Elapsed']; ?></td></tr>
            <tr><td>Finished</td><td><?php echo $results[0]['Finished']; ?></td></tr>
            <tr><td>Score</td><td><?php echo $results[0]['Score']; ?></td></tr>
            <tr><td>Sequence</td><td><?php echo str_replace(',',' ',$results[0]['Seq']); ?></td></tr>
            <?php
            for ($x = 1; $x < 61; $x++) {
                $idx = 'Q'+$x;
                echo "<tr><td>Q" . $x . "</td><td>" . $results[0]['Q' . $x] . "</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>