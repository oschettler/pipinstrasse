<?php
$random_photo = $this->random_photo();
if ($random_photo) {
  ?>
  <a href="/photo/view/<?php echo $random_photo->id; ?>"><img src="/photo/scaled/<?php echo $random_photo->id; ?>/230x" /></a>
  <?php
}
else {
  ?>
  <a href="/photo/index">Laden Sie ein Foto hoch!</a>
  <?php
}
