<html>
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8">
  <title>Pipinstraße-Chat</title>
  <style type="text/css" media="screen">
    * {
      font-family: arial;
    }
    #messages {
      height: 440px;
      background-color: #EEE;
      margin-bottom: 10px;
      list-style-type: none;
      padding: 0;
      border: solid 1px #DDD;
      overflow: auto;
    }
    
    #messages li {
      padding: 4px;
    }
    
    li:nth-child(even) { background-color: #EEE }
    li:nth-child(odd) { background-color: #FFF }
    
    #input {
      width: 100%;
      font-size: 1.1em;
    }
  </style>
  <script src="<?php echo $config['static_url']; ?>/js/jquery-1.6.1.min.js" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript" charset="utf-8">
  var last = 0;
  
  function update() {
    $.getJSON('/chat/update/' + last, function(data) {
      $('#users span').html(data.users.join(' '));
      if (data.messages.length > 0) {
        $('#messages')
          .append('<li>' + data.messages.join('</li><li>') + '</li>')
          .scrollTop($('#messages').height());
        last = $('#messages li:last a').attr('p:id');
      }
    });
  }
  
  jQuery(function($) {
    // Update alle 3s
    setInterval(update, 3000);
    update();

    $('form').submit(function() {
      $.post('/chat/write', {
        input: $('#input').val()
      }, update);
      input: $('#input').val('');
      return false;
    });
    
    $('#messages a').live('click', function() {
      $('#input')
        .val('@' + $(this).text() + ': ')
        .focus();
      return false;
    });
  });
  </script>
</head>
<body id="chat">
  <ul id="messages"></ul>
  <form accept-charset="utf-8">
    <input id="input" type="text" name="input">
  </form>
  <p id="users">Nachbarn im Chat: <span></span></p>
</body>
</html>