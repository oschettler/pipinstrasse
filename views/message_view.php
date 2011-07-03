<?php
if (!empty($message->gift)) {
  ?><h2>Ein Geschenk für dich</h2>
  <img src="/gifts/<?php echo $message->gift; ?>.png" />
  <?php
}
?>
<p>
  <?php echo $this->reltime($message->m_created), ': ', nl2br(preg_replace('#(http://(\S+))#', '<a href="$1">$2</a>', strip_tags($message->nachricht))); ?>
<p>
<?php echo $this->user_link($message); ?> antworten.