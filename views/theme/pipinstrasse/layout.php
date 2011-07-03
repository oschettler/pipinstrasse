<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title> 
<meta name="viewport" content="width=1030" /> 
<link href="http://static.pipinstrasse.de/css/design.css" rel="stylesheet" type="text/css" media="screen" /> 
<script src="http://static.pipinstrasse.de/js/jquery-1.6.1.min.js" type="text/javascript" charset="utf-8"></script>
<?php echo $this->render('_head_javascript'); ?>
<?php
if ($page_head) {
  echo join("\n", $page_head);
}
?>
</head>
<body>
  <?php 
  if (!empty($_SESSION['user']) && !$_SESSION['user']->guest) {
    $this->render('_chat');
  }
  echo $this->render('_status_message'); 
  ?>
  <div id="page">
    <header>
      <img id="logo" src="http://static.pipinstrasse.de/img/pipinstrasse_logo.png" alt="Pipinstraße" />  
      
      
      
      
      <?php
       if (!empty($_SESSION['user'])) {
       ?>
      <div id="usermenu">
           <?php echo $this->user_link(); ?>
           | <a href="/user/edit">Bearbeiten</a>
           | <a href="/message/index">Nachrichten</a><?php
           if ($message_count) { echo " <span title=\"ungelesene Nachrichten\">({$message_count})</span>"; }
           ?>
           | <a href="/user/logout">Abmelden</a>        
         
      </div>
      <?php
       }
       ?>
         
      
      
      
      
      <?php
      if (!empty($_SESSION['user'])) {
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
        <?php echo $contents; ?>
      </section>
      <aside id="sideInfo">
        <!-- RANDOM PICTURE -->
        <h2>Foto aus der Nachbarschaft</h2>
        <?php
        $random_photo = $this->random_photo();
        ?>
        <a href="/photo/view/<?php echo $random_photo->id; ?>"><img src="/photo/scaled/<?php echo $random_photo->id; ?>/230x" /></a>
        
        
        <!-- Online --> 
         <?php $this->render('_online'); ?>
         
         
         <!-- Kurz vorgestellt -->
         <?php
         $random_user = $this->random_user(); 
         ?>
         <h2><span>Kurz vorgestellt:</span><a href="<?php echo $this->user_link($random_user, /*url_only*/TRUE); ?>"><?php echo "{$random_user->vorname} {$random_user->nachname}"; ?></a></h2>
         <p>
           <?php
           if (file_exists($this->image($random_user->id, NULL, 'avatars'))) {
             echo "<img id=\"avatar\" src=\"/user/avatar/{$random_user->id}\">";
           }
           $bio = nl2br(preg_replace('#(http://(\S+))#', '<a href="$1">$2</a>', strip_tags($random_user->bio)));
           if (!empty($bio)) {
             ?>
             <strong>Ich bin&hellip;</strong><br />
             <?php echo $bio; ?>
             <?php
           }
           ?>
         </p>
        
      </aside>
    </section> 
    <footer>   
      <p>
        <a href="http://pages.pipinstrasse.de/kontakt">Kontakt</a>
        &nbsp;
        <a href="http://pages.pipinstrasse.de/impressum">Impressum</a>
      </p>
      <p>
        <?php $this->render('_theme'); ?>
      </p>
    </footer>
  </div>
</body>
</html>
