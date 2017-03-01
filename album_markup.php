<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Example of Bootstrap 3 Thumbnails</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<style type="text/css">
.container {
  position: relative;
}

.demo-content{
    padding: 2px;
    font-size: 18px;
    /*min-height: 1px;*/
    background: #dbdfe5;
    margin-bottom: 10px;
}

    .demo-content img {
      width: 100%;
    }
    .demo-content.bg-alt{
        background: #abb1b8;
    }
	.hover-button {
     position: absolute;
     bottom: 15px;
     left:50px;
     display: none;
	}
	/*.demo-content:hover
	{
	    opacity: 0.5;
    filter:alpha(opacity=100);
	}*/
	.demo-content:hover .hover-button {display: block;}
  .no-gutter [class*="-6"] {
      padding-left:0;
  }
  .media {
      display: inline-block;
      width: 100px;
      height: auto;
  }

  img {
    width: 100%;
    height: 100%;
    display: inline-block;
}
  #modal {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    width: 400px;
    /*height: auto;*/
}
</style>

<script type="text/javascript">
$(document).ready(function() {
    $('.ToggleButton').click(function(e) {
		e.preventDefault();
//		alert($(this).attr('id'))
		var content = $(this).html()
		var n = content.search("empty")
    //alert ($(this).attr('href'))

    var tmp = $(this)

    $.ajax({
      url: $(this).attr('href'),
      success: function(data) {
        if (data == 'True')

          {
          tmp.html("<span class=\"glyphicon glyphicon-star\" aria-hidden=\"true\"></span>");
        }
    		else
    			{tmp.html("<span class=\"glyphicon glyphicon-star-empty\" aria-hidden=\"true\"></span>")}
      }});


    });


/*
		if (n>0)
			{$(this).html("<span class=\"glyphicon glyphicon-star\" aria-hidden=\"true\"></span>")}
		else
			{$(this).html("<span class=\"glyphicon glyphicon-star-empty\" aria-hidden=\"true\"></span>")}
    });
*/
    });

    $('.media').hover(function(){
    $(this).css({width:"200%",height:"200%"});
    },function(){
    $(this).css({width:"100%",height:"100%"});
    });


$(function() {
    var currentMousePos = { x: -1, y: -1 };
    $(document).mousemove(function (event) {
        currentMousePos.x = event.pageX;
        currentMousePos.y = event.pageY;
        if($('#modal').css('display') != 'none') {
            $('#modal').css({
                top: currentMousePos.y,
              left: currentMousePos.x + 12
            });
        }
    });
    $('.image').on('mouseover', function() {
      console.log('mouseover');
        var image = $(this).find('img');
        console.log($(window).width()/3)
        console.log(image.attr('src'))
        var modal = $('#modal');
        //$(modal).html(image.clone());
        $(modal).html("<img src = " + image.attr('src') + ">");
        $(modal).css({
            top: currentMousePos.y,
            left: currentMousePos.x + 12,
            width: $(window).width()/2
        });
        $(modal).show();

    });
    $('.image').on('mouseleave', function() {
        $(modal).hide();
    });
});



</script>

</head>
<body>


<div class="container">
    <!--Row with two equal columns-->
    <div class="row no-gutter">



<?php
include 'settings.php';

// Connecting, selecting database
$dbconn = pg_connect("host=localhost dbname=album user=album password=album")
    or die('Could not connect: ' . pg_last_error());

$query = "SELECT a.name, b.* FROM albums a join media b on a.id = b.album_id and a.slug = '" . $_GET['album'] ."' order by b.createddate;";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
/*  foreach ($line as $col_value) {
    echo $col_value;
  }
*/
?>

<div class="col-sm-3 no-padding">
    <div class="demo-content">

<?php
switch ($line["media"]) {
    case 'jpg':
        echo "<a href = 'media.php?mediaid=" . $line["id"] . "' class = 'image'><img src = '" . $thumbs . $line["thumb_small"] . "' class = 'media'></a>";
        break;
    case 'mp4':
        echo '<video width="640" height="480" controls>';
        echo '<source src="' . $originelen . $line["original"]. '" type="video/mp4">';
        echo 'Your browser does not support the video tag.';
        echo '</video>';
        break;
}
?>

<div class="hover-button">
    <a href = '{% url "album:Toggle_AlbumMedia_IsFavorite" photo.id %}' class = 'ToggleButton' class="linky">
      <?php if $line["favorite"] then { ?>
        <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
      <?php } else { ?>
        <span class="glyphicon glyphicon-star-empty" aria-hidden="true"></span>
      <?php } ?>
    </a>
</div>
</div>
</div>
<?php
}

// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);
?>





</div>
<div id='modal'></div>
</body>
</html>
