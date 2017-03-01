<?php
include 'settings.php';

// Connecting, selecting database
$dbconn = pg_connect("host=localhost dbname=album user=album password=album")
    or die('Could not connect: ' . pg_last_error());

$query = "SELECT * FROM albums";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
  foreach ($line as $col_value) {
    echo $col_value;
  }

  echo "<a href = 'album.php?album=" . $line["slug"] . "'>" . $line["name"] . "</a><br>";
}

// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);

?>
