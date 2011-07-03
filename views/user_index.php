<?php
if ($users) {
  $hausnummer = 0;
  ?>
  <ul id="users">
  <?php
  $separator = '';
  foreach ($users as $user) {
    if ($hausnummer != $user->hausnummer) {
      echo "{$separator}<li class=\"hausnummer\"><h2>Hausnummer {$user->hausnummer}</h2><ul>";
      $separator = '</ul></li>';
      $hausnummer = $user->hausnummer;
    }
    ?>
    <li>
      <?php echo $this->user_link($user); ?>
    </li>
    <?php
  }
  echo $separator;
  ?>
  </ul>
  <hr>
  <div id="paginate">
    <p><?php echo $paginate['count']; ?> Nachbarn. Seite <?php echo $paginate['page']; ?> von <?php echo $paginate['page_count']; ?></p> 
    <p>
      <?php echo join(' | ', $paginate['links']); ?>
    </p>
  </div>
  <?php
}
else {
  echo "Keine Nachbarn";
}
?>
