<ul id="photos">
<?php
foreach ($photos as $_photo) {
  $geo = $this->method == 'full' ? 'x50' : '100x100';
  ?>
  <li<?php if ($_photo->current) { echo ' class="current"'; } ?>>
    <a href="/photo/<?php echo $this->method; ?>/<?php echo $_photo->id; ?>"><img class="photo" src="/photo/scaled/<?php echo "{$_photo->id}/{$geo}"; ?>" /></a> 
  </li>
<?php
}
?>
</ul>
<a href="/photo/full/<?php echo $photo->p_id; ?>">Diashow</a>
<?php
if (!$_SESSION['user']->guest && $_SESSION['user']->id == $photo->u_id) {
  ?> | <a id="invite-link" href="/user/guests/<?php echo $photo->topic_id; ?>">Gäste hierher einladen</a><?php
}
?>
