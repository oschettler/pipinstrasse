<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8">
  <title><?php echo $title; ?></title>

  <style type="text/css" media="screen">
  body {
    font-family: arial, helvetica, sans-serif;
  }
  </style>
  <script src="http://static.pipinstrasse.de/js/jquery-1.6.1.min.js" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript" charset="utf-8">
    jQuery(function($) {
      // Verstecke Statusmeldungen nach 3s
      setTimeout(function() {
        $('.message').slideUp('fast');
      }, 3000);
      
      $('a.confirm').click(function() {
        return confirm('Wirklich ' + $(this).text() + '?');
      });
    });
  </script>
</head>
<body id="admin">
  <?php
  if ($msg = $this->message()) {
    echo "<div class=\"message {$msg['class']}\">{$msg['text']}</div>\n";
  }
  echo $this->render('_nav', array('navigation' => array(
    '/' => 'Frontend',
    '/admin' => 'Admin Startseite',
    'http://schettler.net/fossil/pipinstrasse' => 'Fossil Repository',
    '/admin/migrate' => array('confirm' => TRUE, 'Datenbank aktualisieren'),
    '/admin/move_photos' => array('confirm' => TRUE, 'Fotos verschieben'),
  )));
  
  ?>
  <h1><?php echo $title; ?></h1>
  <pre><?php echo $contents; ?></pre>
</body>
</html>