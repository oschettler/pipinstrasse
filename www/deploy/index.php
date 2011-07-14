<?php
define('DEPLOY_SCRIPT', '/var/deploy/pipinstrasse.sh');
define('DEPLOY_LOG', '/var/deploy/pipinstrasse.log');

error_reporting(E_ALL);
ini_set('display_errors', TRUE);

$title = 'Veröffentlichen';
ob_start();
if (empty($_POST) || $_POST['confirm'] != 'ja') {
  if (file_exists(DEPLOY_LOG)) {
    $out = array();
    exec('tail ' . DEPLOY_LOG, $out);
    echo '<pre>', join('<br>', $out), '</pre>';
  }
  else {
    echo "Bisher kein " . DEPLOY_LOG;
  }
}
else {
  set_time_limit(0);
  $root = realpath("{$_SERVER['DOCUMENT_ROOT']}/..");
  
  $script = <<<EOS
cd {$root}
git pull
mkdir -p import www/img/photos www/img/avatars
echo Done.
EOS;
    
  if (@filemtime(DEPLOY_SCRIPT)) {
    $message = strftime('Das letzte Script wartet seit %Y-%m-%d %H:%M:%S auf Ausführung.');
  }
  else {
    $now = strftime('%Y-%m-%d %H:%M:%S');
    file_put_contents(DEPLOY_SCRIPT, "#!/bin/bash\n#since {$now}\n\n" . $script);
    chmod(DEPLOY_SCRIPT, 0664);
    $message = "{$now}: Skript zur Veröffentlichung angelegt.";
  }
}
?>
<form method="POST">
  Wollen Sie den aktuellen Stand veröffentlichen?
  <input name="confirm" type="submit" value="ja">
  <input name="confirm" type="submit" value="nein">
</form>
<?php
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
  
  form {
    margin-top: 20px;
    padding-top: 10px;
    border-top: solid 1px black;
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