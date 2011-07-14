<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);

$title = 'Veröffentlichen';
ob_start();
if (empty($_POST) || $_POST['confirm'] != 'ja') {
  ?>
  <form method="POST">
    Wollen Sie den aktuellen Stand veröffentlichen?
    <input name="confirm" type="submit" value="ja">
    <input name="confirm" type="submit" value="nein">
  </form>
  <?php
}
else {
  set_time_limit(0);
  $root = realpath("{$_SERVER['DOCUMENT_ROOT']}/..");
  exec("cd {$root}; git pull; mkdir -p import www/img/photos www/img/avatars; echo Done.", $out, $status);
  if ($status == 0) {
    $message = 'Erfolgreich veröffentlicht:<br>';
    $message .= join('<br>', $out);
  }
  else {
    $message = 'Fehler:<br>';
    $message .= join('<br>', $out);
    $status = 'error';
  }
}
$contents = ob_get_clean();
?><!DOCTYPE html>
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
    width: 920px;
    min-height: 600px;
    margin: auto;
    background-color: white;
    padding: 20px;
  }

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
</head>
<body>
  <?php
  if (!empty($message)) {
    ?>
    <div class="message <?php echo empty($status) ? 'status' : $status; ?>"><?php echo $message; ?></div>
    <?php
  }
  ?>
  <div id="wrapper">
    <h1><?php echo $title; ?></h1>
    <?php echo $contents; ?>
  </div>
</body>
</html>