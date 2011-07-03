<?php
if (!empty($intro)) {
  echo "<p>{$intro}</p>";
}
?>
<form method="POST" accept-charset="utf-8">
  
  <label for="emails">E-Mail-Adressen</label>
  <textarea name="emails" rows="4" cols="40"></textarea>
  <p class="explain">Bitte schreiben Sie jede Adresse in eine neue Zeile</p>
  
  <label for="message">Ihre Nachricht</label>
  <textarea name="message" rows="9" cols="40"><?php echo $_POST['message']; ?></textarea>  
  <p class="explain">Verwenden Sie den vorgegebenen Text oder fügen Sie eine eigene Nachricht ein</p>

  <input type="submit" value="Senden">
</form>