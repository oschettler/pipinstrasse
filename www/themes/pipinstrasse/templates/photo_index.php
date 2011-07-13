<?php
$this->page_head();
?>
<script type="text/javascript" charset="utf-8">
jQuery(function($) {
  // Zeige Metadaten eines Fotos beim Drüberfahren mit der Maus
  $('div.photo').hover(function() {
    
    // Alle anderen entfernen
    $('div.vignette').css({ opacity: 0 });
    
    // Aktuelle sanft einblenden
    var $me = $('div.vignette', this);
    $me.animate({ opacity: 1 }, 200);
      
    // Nach 3s hängende Vignetten abschiessen
    setTimeout(function() {
      $me.css({ opacity: 0 });
    }, 3000);
    
  }, function() {

    // Wieder ausblenden
    $('div.vignette', this).css({ opacity: 0 });
  });
});
</script>
<?php
$this->end_page_head();
?>
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
      <div class="vignette">
        <?php
        if ($_SESSION['user']->id == $photo->u_id) {
          echo "<a class=\"edit\" href=\"/photo/edit/{$photo->p_id}\" title=\"eigenes Foto bearbeiten\">✐</a>";
        }
        ?>
        <a href="/photo/view/<?php echo $photo->p_id; ?>"><img src="/photo/scaled/<?php echo $photo->p_id; ?>/200x200" /></a> 
        <div class="meta">
          <?php
          echo '<span class="user">', $this->user_link($photo), '</span><span class="date">', $this->reltime($photo->p_created), '</span>';

          if ($photo->topic_id) {
            ?><span class="topic"><?php echo $photo->topic; ?></span><?php
          }

          if ($photo->comment_count) {
            ?><span class="comments"><?php echo $this->plural($photo->comment_count, '%d Kommentare', '1 Kommentar'); ?></span><?php
          }
          ?>
        </div>
      </div>
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
