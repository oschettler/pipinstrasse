<form action="/message/contact" method="POST">
  <label for="text">Ihre Nachricht an die Seitenbetreiber</label>
  <textarea name="nachricht" rows="8" cols="40"></textarea>
  
  <?php
  if (empty($_SESSION['user']) || $_SESSION['user']->guest) {
    ?>
    <label for="captcha">Zu Vermeidung von Spam: <?php echo "{$captcha['text']} = "?></label><input type="text" name="captcha" value="" id="captcha">
    <p>Bitte lösen Sie diese Rechenaufgabe. Sie helfen uns dadurch Spam zu vermeiden.</p>
    <?php
  }
  ?>
  
  <input type="submit" value="Nachricht abschicken">
</form>
