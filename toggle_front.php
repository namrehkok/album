<?php
if ($_GET['mediaid'] and $_GET['album'])
{
  // Connecting, selecting database
  $dbconn = pg_connect("host=localhost dbname=album user=album password=album")
      or die('Could not connect: ' . pg_last_error());

  #Update the front of the album
  $query = "update albums set front = " . $_GET['mediaid'] ." where slug = '" . $_GET['album'] ."';";
  $result = pg_query($query) or die('Query failed: ' . pg_last_error());


  echo $_GET['album'] .  ' is updated with ' . $_GET['mediaid'];

  // Free resultset
  pg_free_result($result);

  // Closing connection
  pg_close($dbconn);
}
else {
  echo 'no mediaid given';
}
?>
