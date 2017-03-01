<?php
if ($_GET['mediaid'])
{
  // Connecting, selecting database
  $dbconn = pg_connect("host=localhost dbname=album user=album password=album")
      or die('Could not connect: ' . pg_last_error());

  #First remove all the fronts from the album
  $query = "
  update media set isfront = false where id in
  (select id from media a join
  (select album_id from media where id = " . $_GET['mediaid'] .") b on a.album_id = b.album_id);
  ";
  $result = pg_query($query) or die('Query failed: ' . pg_last_error());

  #Update the chosen
  $query = "update media set isfront = true where id = " . $_GET['mediaid'] .";";
  $result = pg_query($query) or die('Query failed: ' . pg_last_error());


  echo $_GET['mediaid'] . ' is updated';

  // Free resultset
  pg_free_result($result);

  // Closing connection
  pg_close($dbconn);
}
else {
  echo 'no mediaid given';
}
?>
