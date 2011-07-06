<?php
if ($msg = $this->message()) {
  echo "<div class=\"message {$msg['class']}\"><p>{$msg['text']}</p></div>\n";
}
?>
