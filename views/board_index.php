<form method="POST">
  <label for="nachricht">Geben Sie hier Ihre Nachricht ein.</label>
  <textarea name="nachricht" rows="8" cols="40"></textarea>
  <input type="submit" value="Schreiben">
</form>

<hr>

<?php
if ($messages) {
  ?>
  <ul id="board">
  <?php
  foreach ($messages as $message) {
    ?>
    <li>
      <?php echo $this->user_link($message), ' schrieb ', $this->reltime($message->m_created); ?>:
      <span> 
        <?php echo nl2br(preg_replace('#(http://(\S+))#', '<a href="$1">$2</a>', strip_tags($message->nachricht))); ?>
        </span>
    </li>
    <?php
  }
  ?>
  </ul>
  <?php
  require '_paginate.php';
}
else {
  echo "<p>Keine Einträge</p>";
}
?>
