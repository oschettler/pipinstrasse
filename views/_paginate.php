<hr>
<div id="paginate">
  <p><?php echo $paginate['count'] == 1 ? 'Ein Eintrag' : "{$paginate['count']} Einträge"; ?>. Seite <?php echo $paginate['page']; ?> von <?php echo $paginate['page_count']; ?></p> 
  <p>
    <?php echo join(' | ', $paginate['links']); ?>
  </p>
</div>
