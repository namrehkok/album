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

// get the sorting order of the pictures
switch ( $_GET['sorting'])
{
  case 'desc':
    $sorting = 'desc';
    break;
  default:
    $sorting = 'asc';
    break;
}


// Only getting the favorite pictures in case the favorite=1 was set in URL
switch ( $_GET['favorite'] )
  {
    case '1':
      $query = "SELECT a.name, b.* FROM albums a join media b on a.id = b.album_id and b.favorite = True and a.slug = '" . $_GET['album'] ."' order by b.createddate " . $sorting . ";";
      break;
    default:
      $query = "SELECT a.name, b.* FROM albums a join media b on a.id = b.album_id and a.slug = '" . $_GET['album'] ."' order by b.createddate " . $sorting . ";";
      break;
  }


$result = pg_query($query) or die('Query failed: ' . pg_last_error());

$data = pg_fetch_all($result);

// Sending data to template
echo $twig->render('album.twig', array('data' => $data, 'thumbs' => $thumbs,
        'originelen' => $originelen,
        'url_media' => $url_media,
        'url_toggle_favorite' => $url_toggle_favorite,
        'url_toggle_front' => $url_toggle_front,
        'url_album' => $url_album,
        'album' => $_GET['album']
      ));

// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);

include 'footer.html';
?>
