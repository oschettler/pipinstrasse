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
    | <a href="/message">Nachrichten</a><?php
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
