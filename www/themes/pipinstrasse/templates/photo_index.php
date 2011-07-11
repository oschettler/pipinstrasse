<?php
$this->render('photo_edit');
?>
<h2>Letzte Bilder</h2>
<?php
if ($photos) {
  ?>
  <div id="photos">
  <?php
  foreach ($photos as $photo) {
    ?>
    <div class="photo">
      <?php
      if ($_SESSION['user']->id == $photo->u_id) {
        echo "<a class=\"edit\" href=\"/photo/edit/{$photo->p_id}\" title=\"eigenes Foto bearbeiten\">✐</a>";
      }
      ?>
      <a title="Detailansicht mit Kommentaren" href="/photo/view/<?php echo $photo->p_id; ?>"><img class="photo" src="/photo/scaled/<?php echo $photo->p_id; ?>/100x100" /></a> 
    </div>
    <?php
  }
  ?>
  </div><!-- #photos -->
  <?php
  require '_paginate.php';
}
else {
  echo "Keine Fotos";
}
?>
