<?php
// Connecting, selecting database
$dbconn = pg_connect("host=localhost dbname=album user=album password=album")
    or die('Could not connect: ' . pg_last_error());

$query = "select * from media where id = " . $_GET['mediaid'] .";";

$result = pg_query($query) or die('Query failed: ' . pg_last_error());

$data = pg_fetch_all($result);

switch ( $data[0]["favorite"] )
  { case 't':
      $return = 'False';
    break;
    case 'f':
      $return = 'True';
    break;
  }
;

$query = "update media set favorite = " . $return . " where id = " . $_GET['mediaid'] . ";";

$result = pg_query($query) or die('Query failed: ' . pg_last_error());


echo $return;

// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);
?>
