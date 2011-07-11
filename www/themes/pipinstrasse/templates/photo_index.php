<?php
$this->render('photo_edit');
?>
<h2>Letzte Bilder</h2>
<?php
if ($photos) {

  foreach ($photos as $photo) {
      if ($_SESSION['user']->id == $photo->u_id) {
        echo "<a class=\"edit\" href=\"/photo/edit/{$photo->p_id}\" title=\"eigenes Foto bearbeiten\">✐</a>";
      }
      ?>
      <a href="/photo/view/<?php echo $photo->p_id; ?>"><img class="photo" src="/photo/scaled/<?php echo $photo->p_id; ?>/100x100" /></a> 
      <?php
  }    
  
  ?>
  <?php
  require '_paginate.php';
}
else {
  echo "Keine Fotos";
}
?>
