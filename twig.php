<?php

require_once __DIR__ . '/vendor/autoload.php';

$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates//');
$twig = new Twig_Environment($loader);


$dbconn = pg_connect("host=localhost dbname=album user=album password=album")
    or die('Could not connect: ' . pg_last_error());

$query = "SELECT * FROM media b;";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

$t = array();

$t = pg_fetch_all($result);


echo $twig->render('index.html', array('data' => $t));

// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);
?>
