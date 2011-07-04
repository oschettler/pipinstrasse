<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8">
  <title><?php echo $title; ?></title>

  <style type="text/css" media="screen">
  body {
    font-family: arial, helvetica, sans-serif;
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
  
  </style>
  <link rel="stylesheet" href="<?php echo $config['static_url']; ?>/jquery-ui/css/smoothness/jquery-ui-1.8.14.custom.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <?php echo $this->render('_head_javascript'); ?>
  <?php
  if ($page_head) {
    echo join("\n", $page_head);
  }
  ?>
</head>
<body id="admin">
  <?php
  if ($msg = $this->message()) {
    echo "<div class=\"message {$msg['class']}\">{$msg['text']}</div>\n";
  }
  echo $this->render('_nav', array('navigation' => array(
    '/' => 'Frontend',
    '/admin' => 'Admin Startseite',
    '/admin/users' => 'Nutzer verwalten',
    '/admin/topics' => 'Alben verwalten',
    // 'http://schettler.net/fossil/pipinstrasse' => 'Fossil Repository',
    '/admin/migrate' => array('confirm' => TRUE, 'Datenbank aktualisieren'),
    // '/admin/move_photos' => array('confirm' => TRUE, 'Fotos verschieben'),
  )));
  
  ?>
  <h1><?php echo $title; ?></h1>
  <?php echo $contents; ?>
</body>
</html>