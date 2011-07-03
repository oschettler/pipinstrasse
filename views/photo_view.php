<img id="photo" src="/photo/scaled/<?php echo $photo->p_id; ?>/670x600" >
<?php
if ($_SESSION['user']->guest) {
  return;
}
?>
<p id="info">
  <?php
  if (strtotime($photo->p_updated) > strtotime($photo->p_created)) {
    ?>
    Geändert von <?php echo $this->user_link($photo), ' ', $this->reltime($photo->p_updated); ?>
    <?php
  }
  else {
    echo 'Hochgeladen von ', $this->user_link($photo), ' ', $this->reltime($photo->p_created); 
  }
  if ($photo->topic) {
    ?><br>im Album <strong><?php echo $photo->topic; ?></strong><?php
  }
  echo '. ';

  if ($photo->von == $_SESSION['user']->id) {
    echo "<a href=\"/photo/edit/{$photo->p_id}\">Ändern</a>";
  }
  ?>
</p>

<h2>Kommentare</h2>
<form action="/comment/add" method="POST" accept-charset="utf-8">
  <input type="hidden" name="type" value="photo">
  <input type="hidden" name="id" value="<?php echo $photo->p_id; ?>">
  <input type="hidden" name="an" value="<?php echo $photo->id; ?>">
  <input type="hidden" name="url" value="<?php echo $_GET['url']; ?>">

  <label for="like"></label>+1<input type="checkbox" name="like" value="1">

  <textarea name="comment" rows="4" cols="40"></textarea>

  <input type="submit" value="Speichern">
</form>
<hr>
<?php
if ($comments) {
  ?>
  <ul id="comments">
    <?php
    foreach ($comments as $comment) {
      echo "<li>", $this->user_link($comment), ' ', $this->reltime($comment->c_created), ': ', $comment->comment, ' ', $comment->liked ? '<strong>+1</strong>' : '', '</li>';
    }
    ?>
  </ul>
  <?php
  require '_paginate.php';
}
else {
  echo '<p>Keine</p>';
}
?>