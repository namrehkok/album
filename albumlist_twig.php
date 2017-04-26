<?php
include 'settings.php';
include 'header.html';

// Setting up Twig
require_once __DIR__ . '/vendor/autoload.php';
$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates//');
$twig = new Twig_Environment($loader);

// Connecting, selecting database
$dbconn = pg_connect("host=localhost dbname=album user=album password=album")
    or die('Could not connect: ' . pg_last_error());

$query = "SELECT a.name, a.slug,  min(cast(b.createddate as date)) as startdate, max(cast(b.createddate as date)) as enddate,
b.thumb_small as thumb_small
FROM albums a left join media b on a.id = b.album_id and a.front = b.id group by 1 ,2, 5 order by coalesce(max(cast(b.createddate as date)), to_date('18000101', 'yyyymmdd')) desc";

$result = pg_query($query) or die('Query failed: ' . pg_last_error());

$data = pg_fetch_all($result);

// Sending data to template
echo $twig->render('albumlist.twig', array('data' => $data, 'thumbs' => $thumbs, 'originelen' => $originelen, 'url_album' => $url_album));

// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);

include 'footer.html';
?>
