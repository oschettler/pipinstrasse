<?php
$this->render('photo_edit');
?>

<?php
if ($photos) {
  ?>
  <ul id="photos">
  <?php
  foreach ($photos as $photo) {
    ?>
    <li>
      <h2><?php echo "<a href=\"/photo/view/{$photo->p_id}\">", strip_tags($photo->title), '</a>'; ?></h2>
      <?php 
      if ($_SESSION['user']->id == $photo->u_id) {
        echo "<a class=\"edit\" href=\"/photo/edit/{$photo->p_id}\" title=\"eigenes Foto bearbeiten\">✐</a>";
      }
      ?>
      <a href="/photo/view/<?php echo $photo->p_id; ?>"><img class="photo" src="/photo/scaled/<?php echo $photo->p_id; ?>/100x100" /></a> 
      <?php
      echo 'Von ', $this->user_link($photo), ' hochgeladen ', $this->reltime($photo->p_created);

      if ($photo->topic_id) {
        ?><br>im Album <strong><?php echo $photo->topic; ?></strong><?php
      }
      ?>.
    </li>
    <?php
  }
  ?>
  </ul>
  <?php
  require '_paginate.php';
}
else {
  echo "Keine Fotos";
}
?>
