<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="/themes/tastelessly/style.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/themes/tastelessly/local.css" rel="stylesheet" type="text/css" media="screen" />
<?php echo $this->render('_head_javascript'); ?>
<?php
if ($page_head) {
  echo join("\n", $page_head);
}
?>
</head>
<body id="<?php echo "{$this->name}-{$this->method}"; ?>">
  <?php echo $this->render('_status_message'); ?>
  <?php
  if (!empty($_SESSION['user']) && !$_SESSION['user']->guest) {
    $this->render('_chat');
  ?>
  <?php
  }
  ?>
  <?php $this->blocks('above'); ?>
  <?php echo $contents; ?>
</body>
</html>
