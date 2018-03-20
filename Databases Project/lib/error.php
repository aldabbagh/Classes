<?php if ($error_msg) {  ?>
<div class='error'>
   <div class='error_msg'>
    <?php
      foreach ($error_msg as $error) {
        echo $error . NEWLINE;
       }
    ?>
  </div>
</div>
<?php  } ?>

<?php echo mysqli_error($db); ?>

<?php if ($query_msg) {  ?>
<div class='query'>
  <div class=query_msg'>
    <?php
      foreach ($query_msg as $query) {
          echo $query . NEWLINE;
       }
    ?>
  </div>
</div>
<?php } ?>
