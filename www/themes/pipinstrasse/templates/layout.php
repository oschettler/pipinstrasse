<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title> 
<meta name="viewport" content="width=1030" /> 
<link href='http://fonts.googleapis.com/css?family=Waiting+for+the+Sunrise' rel='stylesheet' type='text/css'>
<link href="/themes/pipinstrasse/css/main.css" rel="stylesheet" type="text/css" media="screen" /> 
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
  }
  ?>
  
  <div id="page">
    <header>
      <a href="/">
        <img id="logo" src="<?php echo $config['static_url']; ?>/img/pipinstrasse_logo.png" alt="Pipinstraße" />
      </a>
      
      <?php echo $this->render('_user'); ?>
      
      <?php
      if (!empty($_SESSION['user']) && !$_SESSION['user']->guest) {
      ?>
        
        <div id="menu">
          <?php $this->render('_nav'); ?>
        </div>
        <div id="invite-link">
          <a href="/user/invite">Nachbarn einladen</a>
        </div>
      <?php
      }
      ?>
    </header>
    <aside id="slogan" class="startseite"></aside>  
    <section id="contentPart">
      <section>
        <?php $this->blocks('above'); ?>
        <?php echo $contents; ?>
      </section>
      <?php
      if (empty($_SESSION['user'])) {
      ?>
     
      <?php
      }
      else {
        ?>
        <aside id="sideInfo">
          <?php $this->blocks('sidebar1'); ?>
          <?php $this->blocks('sidebar2'); ?>
        </aside>
        <?php
      }
      ?>
    </section> 
    <footer>   
      <p>
        <a href="/message/contact">Kontakt</a>
        &nbsp;
        <a href="/page/impressum">Impressum</a>
      </p>  
      <?php $this->render('_theme'); ?>
    </footer>
  </div>
</body>
</html>
