<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="/theme/tastelessly/style.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/theme/tastelessly/local.css" rel="stylesheet" type="text/css" media="screen" />
<script src="<?php echo $config['static_url']; ?>/js/jquery-1.6.1.min.js" type="text/javascript" charset="utf-8"></script>
<?php echo $this->render('_head_javascript'); ?>
<?php
if ($page_head) {
  echo join("\n", $page_head);
}
?>
</head>
<body id="<?php echo "{$this->name}-{$this->method}"; ?>">
<!-- start header -->
<?php echo $this->render('_status_message'); ?>
<?php
if (!empty($_SESSION['user']) && !$_SESSION['user']->guest) {
  $this->render('_chat');
?>
<div id="header">
  <div id="invite-link">
    <a href="/user/invite">Nachbarn einladen</a>
  </div>
  <div id="menu">
    <?php $this->render('_nav'); ?>
  </div>
  <!--div id="search">
    <form id="searchform" method="get" action="#">
      <fieldset>
      <input id="s" type="text" name="s" value="" class="text" />
      <input id="x" type="submit" value="Search" class="button" />
      </fieldset>
    </form>
  </div-->
</div>
<?php
}
?>
<div id="logo">
  <h1><a href="/"><?php echo $title; ?></a></h1>
  <h2><?php echo $slogan; ?></h2>
  
  <?php
  if (!empty($_SESSION['user'])) {
    ?>
    <div id="user">
    <?php
    if ($_SESSION['user']->guest) {
      ?><a href="/user/logout">Gastzugang beenden</a><?php
      
    }
    else {
      ?>
      <?php echo $this->user_link(); ?>
      | <a href="/user/edit">Bearbeiten</a>
      | <a href="/message/index">Nachrichten</a><?php
      if ($message_count) { echo " <span title=\"ungelesene Nachrichten\">({$message_count})</span>"; }
      ?>
      | <a href="/user/logout">Abmelden</a>
      <?php
    }
    ?>
    </div>
    <?php
  }
  ?>
</div>
<!-- end header -->
<hr />
<!-- start page -->
<div id="page">
  <!-- start content -->
  <div id="content">
    <?php echo $contents; ?>
  </div>
  <!-- end content -->
  
  <?php
  if (empty($_SESSION['user'])) {
  ?>
  <img id="map"  src="http://maps.google.com/maps/api/staticmap?markers=Pipinstrasse%2C+53111+Bonn&zoom=16&size=470x470&sensor=false" />
  <?php
  }
  else {
    ?>
  <!-- start sidebar one -->
  <div id="sidebar1" class="sidebar">
    <?php $this->blocks('sidebar1'); ?>
  </div>
  <!-- end sidebar one -->
  <!-- start sidebar two -->
  <div id="sidebar2" class="sidebar">
    <?php $this->blocks('sidebar2'); ?>
  </div>
  <!-- end sidebar two -->
  <?php
  }
  ?>
  
  <div style="clear: both;">&nbsp;</div>
</div>
<!-- end page -->
<hr />
<!-- start footer -->
<div id="footer">
  <p>&copy; <?php echo date('Y'); ?> &nbsp;&bull;&nbsp; <a href="/impressum">Impressum</a> | <a href="/kontakt" >Kontakt</a></p>
  <?php $this->render('_theme'); ?>
</div>
<!-- end footer -->
</body>
</html>
