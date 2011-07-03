<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8">
  <title><?php echo $title; ?> | Pipinstraße</title>
  <link rel="stylesheet" href="http://static.pipinstrasse.de/css/design.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <meta name="robots" content="noindex, nofollow">
  <script src="http://static.pipinstrasse.de/js/jquery-1.6.1.min.js" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript" charset="utf-8">
    jQuery(function($) {
      // Verstecke Statusmeldungen nach 3s
      setTimeout(function() {
        $('.message').slideUp('fast');
      }, 3000);

      // Änderen Theme
      $('#switch-theme select').change(function() {
        $(this).parent().submit();
      });
    });
  </script>
</head>
<body>
  <div id="page">
    <header> 
      <div id="logo">
        <a href="/test">
          <img src="http://static.pipinstrasse.de/img/pipinstrasse_logo.png" alt="Pipinstraße" />
        </a>
      </div>
      <menu id="usermenu">
        <?php
        if (!empty($_SESSION['user'])) {
        ?> 

        <ul>
          <li><?php echo $this->user_link($_SESSION['user']); ?>
            <ul>
              <li><a href="/user/edit">Profil bearbeiten</a></li>
              <li><a href="/user/logout">Abmelden</a></li>
            </ul>
          </li>
          <li>
            <a href="/message/index">Nachrichten</a>
            <?php
              if ($message_count) { echo " <span title=\"ungelesene Nachrichten\">({$message_count})</span>"; }
            ?>
          </li> 
        </ul>
        <?php
          }
        ?>
      </menu>
    </header>         
    
    <aside class="<?php echo $slogan; ?>"></aside>  
    <section>
      <?php
      /*
       *  Kopfbild
       */         
       if(!empty($head_src)){
        ?>   
        <img src="http://static.pipinstrasse.de/img/head_<?= $head_src ?>.jpg" alt="<?= $head_title ?>" title="<?= $head_title ?>" />
        <?php
       }
       
      ?>
      
       <?php
    if ($msg = $this->message()) {
      echo "<div class=\"message {$msg['class']}\">{$msg['text']}</div>\n";
    }
    ?>
    
    <?php
    if (!empty($_SESSION['user'])) {
      $this->render('_nav');
      $this->render('_online');
    }
    ?>
        
    <h1><?php echo $title; ?></h1> 
    <?php echo $contents; ?>
    </section> 
    <footer>   
      <p>
        <a href="http://pages.pipinstrasse.de/kontakt">Kontakt</a>
        &nbsp;
        <a href="http://pages.pipinstrasse.de/impressum">Impressum</a>
        &nbsp;
        <?php $this->render('_theme'); ?>
      </p>
    </footer>
  </div> 
</body>
</html>

<?php
/***
<div id="user">
  Angemeldet als 
  <?php echo $this->user_link(); ?>
  | <a href="/user/edit">Bearbeiten</a>
  | <a href="/message/index">Nachrichten</a><?php
  if ($message_count) { echo " <span title=\"ungelesene Nachrichten\">({$message_count})</span>"; }
  ?>
  | <a href="/user/logout">Abmelden</a>
</div>
***/


?>        
