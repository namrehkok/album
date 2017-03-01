<?php
// Connecting, selecting database
$dbconn = pg_connect("host=localhost dbname=album user=album password=album")
    or die('Could not connect: ' . pg_last_error());

$query = "SELECT * FROM media b;";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

$t = array();

$t = pg_fetch_all($result);


foreach ($t as $item)
  {
    foreach ($item as $key => $value) {
        echo "Key: $key; Value: $value<br />\n";
    }
  }


?>
