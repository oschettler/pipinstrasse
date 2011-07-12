<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8">
  <title><?php echo $title; ?></title>

  <style type="text/css" media="screen">
  body {
    font-family: arial, helvetica, sans-serif;
    background-color: #EEE;
    padding: 0;
    margin: 0;
  }
  
  #wrapper {
    width: 940px;
    margin: auto;
    background-color: white;
    padding: 10px;
  }

  tr:nth-child(even) { background-color: #EEE }
  tr:nth-child(odd) { background-color: #FFF }
  
  .message {
    position: absolute; 
    top: 0; 
    left: 0;
    font-size: 1.4em; 
    padding: 10px; 
    width: 100%; 
    z-index: 100;
  }

  .message.status {
    background: #CFC;
  }

  .message.error {
    background: #FCC;
  }
  
  #nav {
    position: relative;
    margin: 20px 10px;
  }
  
  #nav ul {
    list-style-type: none;
    display: none;
    position: absolute;
    top: 24px;
    left: 4px;
    padding: 10px;
    background-color: #888;
    border: solid 2px white;
  }

  #nav a#top {
    background-color: #888;
    border: solid 2px white;
    color: white;
    text-decoration: none;
    padding: 10px;
  }
  
  #nav ul {
    margin: 0;
    padding: 0;
  }
  
  #nav ul a {
    color: white;
    text-decoration: none;
    display: block;
    padding: 4px;
  }
  
  #nav ul a:hover {
    background-color: #444;
  }
  
  textarea {
    display: block;
    width: 100%;
  }
  
  </style>
  <link rel="stylesheet" href="<?php echo $config['static_url']; ?>/jquery-ui/css/smoothness/jquery-ui-1.8.14.custom.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <?php echo $this->render('_head_javascript'); ?>
  <?php
  if ($page_head) {
    echo join("\n", $page_head);
  }
  ?>
  <script type="text/javascript" charset="utf-8">
    jQuery(function($) {
      $('#nav > a').toggle(function() {
        $(this).next('ul').slideDown('fast');
        return false;
      }, function() {
        $(this).next('ul').slideUp('fast');
        return false;
      });
    });
  </script>
</head>
<body id="admin">
  <?php
  if ($msg = $this->message()) {
    echo "<div class=\"message {$msg['class']}\">{$msg['text']}</div>\n";
  }
  echo '<div id="nav"><a id="top" href="#">Verwaltung</a>', $this->render('_nav', array('navigation' => array(
    '/' => 'Frontend',
    '/admin' => 'Admin Startseite',
    '/admin/users' => 'Nutzer verwalten',
    '/admin/pages' => 'Seiten verwalten',
    '/admin/topics' => 'Alben verwalten',
    // 'http://schettler.net/fossil/pipinstrasse' => 'Fossil Repository',
    '/admin/migrate' => array('confirm' => TRUE, 'Datenbank aktualisieren'),
    // '/admin/move_photos' => array('confirm' => TRUE, 'Fotos verschieben'),
  ))), '</div><!--#nav-->';
  ?>
  <div id="wrapper">
    <h1><?php echo $title; ?></h1>
    <?php echo $contents; ?>
  </div><!--#wrapper-->
</body>
</html>