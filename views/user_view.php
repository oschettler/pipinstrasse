<?php
if (file_exists($this->image($user->id, NULL, 'avatars'))) {
  echo "<img id=\"avatar\" src=\"/user/avatar/{$user->id}\">";
}

if (!empty($status)) {
?>
<p id="status">Mein Status (<?php echo $this->reltime($status->created); ?>):
  <?php echo nl2br(preg_replace('#(http://(\S+))#', '<a href="$1">$2</a>', strip_tags($status->title))); ?>
<p>
<?php
}
?>

<p id="bio"><strong>Ich bin&hellip;</strong><br />
  <?php echo nl2br(preg_replace('#(http://(\S+))#', '<a href="$1">$2</a>', strip_tags($user->bio))); ?>
<p>

<hr>
<form action="/message/write" method="POST">
  <input type="hidden" name="an" value="<?php echo $user->id; ?>">

  <label for="text">Nachricht an <?php echo "{$user->vorname}"; ?></label>
  <textarea name="nachricht" rows="8" cols="40"></textarea>

  <a href="#" class="handle">Als Geschenk</a>
  <ul id="gifts" class="folded">
    <?php
    global $config;
    foreach ($config['gifts'] as $gift) {
    ?>
    <li><img src="/gifts/<?php echo $gift; ?>_small.png"><input type="radio" name="gift" value="<?php echo $gift; ?>" /></li>
    <?php
    }
    ?>
  </ul>

  <input type="submit" value="Nachricht abschicken">
</form>
