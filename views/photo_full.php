<?php
$this->page_head();
?>
<style type="text/css" media="screen">
  #bg { position: fixed; top: 0; left: 0; }

  #blocks-above {
    position: fixed;
    bottom: 0;
    left: 0;
    margin: 0;
    padding: 0;
    z-index: 10;
  }
  
  #blocks-above ul {
    background-color: black;
    overflow: hidden;
    margin: 0;
    padding: 10px 0 0 10px;
  }
  
  #blocks-above ul#photos li {
    float: left;
    list-style-type: none;
    margin: 0 10px 6px 0;
  }

  #blocks-above img.photo {
    margin-bottom: 0;
  }
  
  #blocks-above li.current img.photo {
    border-color: white;
  }
  
  #blocks-above h2,
  #links {
    display: none;
  }
  
  #info {
    position: absolute;
    top: 0;
    z-index: 1000;
    background: red;
    color: white;
  }
  
  #ctrl {
    position: absolute;
    top: 0;
    right: 0;
    display: block;
    padding: 4px 10px;
    background-color: black;
    color: white;
  }
  
  #ctrl a {
    color: white;
  }
  
</style>
<script type="text/javascript" charset="utf-8">
var timer;

$(window).load(function() {    
  var w = $(window);
  var $bg = $("#bg");
  var bg_width = $bg.width();
  var bg_height = $bg.height();
  var aspect_ratio = bg_width / bg_height;

  function resize_bg() {
    var bg_top = 0;
    var bg_left = 0;

    if (bg_width > w.width()) {
      bg_left = Math.round((bg_width - w.width()));
      $bg.css({ left: bg_left / -2});
    }
    if (bg_height > w.height()) {
      bg_top = Math.round((bg_height - w.height()));
      $bg.css({ top: bg_top / -2 });
    }

    if ((w.width() / w.height()) < aspect_ratio) {
      $bg.css({ height: w.height() + bg_top, width: 'auto' }); 
    } 
    else {
      $bg.css({ width: w.width() + bg_left, height: 'auto' }); 
    }
    
    $('#info').html('bg.width=' + bg_width + ', bg.height=' + bg_height + ', w.width=' + w.width() + ', w.height=' + w.height() + ', bg.top=' + bg_top + ', bg_left=' + bg_left + ', css.width=' + $bg.css('width') + ', css.height=' + $bg.css('height'));
  }

  w.resize(function() {
    resize_bg();
  }).trigger("resize");
  
  start();
});

function start() {
  timer = setInterval(diashow, 10000);
  $('#startstop')
    .attr('href', 'javascript:stop()')
    .text('stop');
}

function diashow() { 
  var src = $('#bg').attr('src').split('/');
  $.getJSON('/photo/next/' + src[3], function(data) { 
    $('title').text(data.title);
    $('#bg')
      .css({ opacity: 0.0 })
      .attr('src', data.next)
      .animate({ opacity: 1.0 }, 200);
    $('#photos').html(data.links);
  });
}
  
function stop() {
  clearInterval(timer);
  $('#startstop')
    .attr('href', 'javascript:start()')
    .text('start');
  return false;
}
</script
<?php
$this->end_page_head();
?>

<img id="bg" src="/photo/scaled/<?php echo $photo->p_id; ?>">
<div id="ctrl">
  <a href="/photo">Mehr Fotos</a> | <a href="javascript:stop()" id="startstop">stop</a>
</div>
<?php /* ?>/<div id="info"></div><?php */ ?>
