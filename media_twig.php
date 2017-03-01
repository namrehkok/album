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

$query = "SELECT * from media where id = '" . $_GET['mediaid'] ."';";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());
$data = pg_fetch_all($result);

// Sending data to template
echo $twig->render('media.twig', array('data' => $data, 'thumbs' => $thumbs, 'originelen' => $originelen));


// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);
include 'footer.html';
?>
