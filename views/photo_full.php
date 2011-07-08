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
    z-index: 10;
  }
  
  #blocks-above ul {
    background-color: black;
    overflow: hidden;
    padding: 10px 0 0 10px;
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
  $('#ctrl')
    .attr('href', 'javascript:stop()')
    .text('stop');
}

function diashow() {
  var next_img; 
  if (location.pathname == $('#photos a:last').attr('href')) {
    next_img = $('#photos a:first').attr('href');
  }
  else {
    next_img = $('#photos li.current').next('li').children('a').attr('href');
  }
  location.href = next_img;
}
  
function stop() {
  clearInterval(timer);
  $('#ctrl')
    .attr('href', 'javascript:start()')
    .text('start');
  return false;
}
</script
<?php
$this->end_page_head();
?>

<img id="bg" src="/photo/scaled/<?php echo $photo->p_id; ?>/1024x768">
<a href="javascript:stop()" id="ctrl">stop</a>
<?php /* ?>/<div id="info"></div><?php */ ?>
