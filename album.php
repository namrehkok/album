<?php
include 'settings.php';

// Connecting, selecting database
$dbconn = pg_connect("host=localhost dbname=album user=album password=album")
    or die('Could not connect: ' . pg_last_error());

$query = "SELECT a.name, b.* FROM albums a join media b on a.id = b.album_id and a.slug = '" . $_GET['album'] ."';";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
/*  foreach ($line as $col_value) {
    echo $col_value;
  }
*/

switch ($line["media"]) {
    case 'jpg':
        echo "<a href = 'media.php?mediaid=" . $line["id"] . "'><img src = '" . $thumbs . $line["thumb_small"] . "'></a>";
        break;
    case 'mp4':
        echo '<video width="640" height="480" controls>';
        echo '<source src="' . $originelen . $line["original"]. '" type="video/mp4">';
        echo 'Your browser does not support the video tag.';
        echo '</video>';
        break;
}

}

// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);
?>
