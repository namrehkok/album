
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

    $('.toggleFront').click(function(e) {
		e.preventDefault();
//		alert($(this).attr('id'))

    var tmp = $(this)

    $.ajax({
      url: $(this).attr('href'),
      success: function(data) {
          alert('front updated!!')
          }
      });


    });

/*
		if (n>0)
			{$(this).html("<span class=\"glyphicon glyphicon-star\" aria-hidden=\"true\"></span>")}
		else
			{$(this).html("<span class=\"glyphicon glyphicon-star-empty\" aria-hidden=\"true\"></span>")}
    });
*/
    });



/*
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
*/
