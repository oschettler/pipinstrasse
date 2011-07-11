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
  <?php
}
else {
  echo "Keine Nachbarn";
}
?>
