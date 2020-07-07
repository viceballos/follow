<?php
$r=intval($_POST['id']);
$t="http://ubiobio.cl/web/includes/gente2.php?id=$r";
//echo $t;
echo file_get_contents($t);


?>