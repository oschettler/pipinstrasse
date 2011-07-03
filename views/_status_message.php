<?php
if ($msg = $this->message()) {
  echo "<div class=\"message {$msg['class']}\">{$msg['text']}</div>\n";
}
?>
