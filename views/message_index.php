<?php
if ($messages) {
  ?>
  <ul id="messages">
  <?php
  foreach ($messages as $message) {
    ?>
    <li>
       <?php
       if ($message->von_id == $_SESSION['user']->id) {
         echo '<strong>An</strong> ', $this->user_link($message->an);
       } 
       else {
         echo $this->user_link($message);
       }
       echo ' ', $this->reltime($message->m_created); ?>: <?php if ($message->viewed == '0000-00-00 00:00:00') { echo '<strong>Neue</strong> '; }; ?><a href="/message/view/<?php echo $message->m_id; ?>">Nachricht</a>:  
        <?php
        $text = strip_tags($message->nachricht);
        echo $first = substr($text, 0, 60);
        if (strlen($first) < strlen($text)) { echo '&hellip;'; }
        ?>
    </li>
    <?php
  }
  ?>
  </ul>
  <?php
}
else {
  echo "Keine Nachrichten";
}
?>
