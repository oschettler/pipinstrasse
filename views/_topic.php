<ul>
<?php
foreach ($photos as $_photo) {
  ?>
  <li>
    <a href="/photo/view/<?php echo $_photo->id; ?>"><img class="photo" src="/photo/scaled/<?php echo $_photo->id; ?>/100x100" /></a> 
  </li>
<?php
}
?>
</ul>
<?php
if (!$_SESSION['user']->guest && $_SESSION['user']->id == $photo->u_id) {
  ?><a href="/user/guests/<?php echo $photo->topic_id; ?>">Gäste hierher einladen</a><?php
}
?>
