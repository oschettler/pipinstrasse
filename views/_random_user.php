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
