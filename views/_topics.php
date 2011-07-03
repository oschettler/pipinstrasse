<ul>
  <?php
  foreach ($topics as $topic) {
    if (empty($topic->p_id)) {
      continue;
    }
    ?>
    <li><a href="/photo/view/<?php echo $topic->p_id; ?>"><img title="<?php echo addslashes($topic->title); ?>" src="/photo/scaled/<?php echo $topic->p_id; ?>/100x100"></a></li>
    <?php
  }
  ?>
</ul>