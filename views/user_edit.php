<form id="register" method="POST" enctype="multipart/form-data" >
  <label for="mail">E-Mail-Adresse</label>
  <input class="input" type="text" name="mail" value="<?php if (!empty($_POST['mail'])) echo $_POST['mail']; ?>" id="">
  <label for="password">Kennwort</label>
  <input class="input" type="password" name="password" value="" id="">
  <label for="password2">Kennwort (Wiederholung)</label>
  <input class="input" type="password" name="password2" value="" id="">
  
  <label for="vorname">Vorname</label>
  <input class="input" type="text" name="vorname" value="<?php if (!empty($_POST['vorname'])) echo $_POST['vorname']; ?>" id="">
  <label for="nachname">Nachname</label>
  <input class="input" type="text" name="nachname" value="<?php if (!empty($_POST['nachname'])) echo $_POST['nachname']; ?>" id="">
  
  <label for="hausnummer">Hausnummer</label>
  <input class="input" type="text" name="hausnummer" value="<?php if (!empty($_POST['hausnummer'])) echo $_POST['hausnummer']; ?>" id="">
  
  <label for="avatar">Foto</label>
  <input class="input" type="file" name="avatar" value="" id="">

  <label for="bio">Über mich</label>
  <textarea name="bio" rows="8" cols="40"><?php if (!empty($_POST['bio'])) echo $_POST['bio']; ?></textarea>

  <input type="submit" value="<?php echo $this->method == 'do_register' ? 'Konto anlegen' : 'Änderungen speichern'; ?>">
</form>
