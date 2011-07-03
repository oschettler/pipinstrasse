<?php
$users_online = $this->users_online();
if ($users_online) {
?>
  <ul>
  <?php
  foreach ($users_online as $user_online) {
    ?><li><?php echo $this->user_link($user_online); ?></li><?php
  }
  ?>
  </ul>
<?php
}
else {
  echo '<ul><li>Niemand außer Ihnen</li></ul>';
}
?>
