<?php
/***********************************************************************************
Copyright (c) 2011 Olav Schettler <olav@schettler.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
----

This is the MIT Open Source License of http://www.opensource.org/licenses/MIT
***********************************************************************************/

define('USERS_PAGE_SIZE', 4);
define('USER_RESOLUTION', '100x100');
define('USER_EMAIL_RE', '/^[-\.+\w]+@\w[-\w\.]+\w$/');

class user_controller extends controller {
  
  /**
   * Action: Als Nutzer anmelden
   */
  function do_login() { 
    $this->vars['title'] = 'Anmeldung'; 
    $this->vars['slogan'] = 'anmelden'; 
    $this->vars['head_src'] = 'schluessel';
    $this->vars['head_title'] = 'juliaw / photocase.com';

    if (empty($_POST)) {
      $this->render();
    }
    else {
      $sql = 'SELECT * FROM users WHERE active = 1'
          . " AND mail = '" . mysql_real_escape_string($_POST['mail']) . "'"
          . ' AND password = MD5('
          . "'" . mysql_real_escape_string($_POST['password']) . "'"
          . ')';

      $rs = mysql_query($sql);
      $_SESSION['user'] = mysql_fetch_object($rs); 
      
      if ($_SESSION['user']) {
        $_SESSION['user']->guest = FALSE;
      }
      
      $this->redirect();
    }
  }
  
  /**
   * Action: Als Nutzer abmelden
   */
  function do_logout() {
    unset($_SESSION['user']);
    $this->redirect('/');
  }
  
  /**
   * Action: Neues Nutzerkonto anlegen. Muss dann erst freigeschalten werden.
   */
  function do_register() {
    $this->vars['title'] = 'Nutzerkonto anlegen';

    if (!empty($_POST)) {
      $rs = mysql_query('SELECT id FROM users WHERE mail = '
        . "'" . mysql_real_escape_string($_POST['mail']) . "'");
      if (mysql_fetch_object($rs)) { 
        $this->message('Dieses Konto existiert bereits', 'error');
      }
      else {
        if ($this->save()) {
          $this->message("Herzlich willkommen, {$_POST['vorname']}. Ihr Nutzerkonto muss noch freigeschaltet werden.");
          $this->redirect();
        }
      }
    }
    $this->render('user_edit');
  }

  /**
   * Action: Nutzerinfo ansehen und Nachricht schreiben
   */  
  function do_view() {
    if (count($this->path) != 3) {
      $this->message('FALSCHE URL');
      $this->redirect();
    }

    $sql = 'SELECT * FROM users WHERE active = 1'
         . " AND slug = '" . mysql_real_escape_string($this->path[2]) . "'";

    $rs = mysql_query($sql);
    $this->vars['user'] = $user = mysql_fetch_object($rs);
    if (!$user) {
      $this->message('Nachbarn nicht gefunden');
      $this->redirect('/');
    }
     
    $this->vars['title'] = "{$user->vorname} {$user->nachname}, Pipinstraße {$user->hausnummer}";
    
    $sql = 'SELECT * FROM stream WHERE '
      . " object_type = 'status' "
      . " AND von = '{$user->id}' "
      . 'ORDER BY created DESC';

    $rs = mysql_query($sql); 
    $this->vars['status'] = mysql_fetch_object($rs);
    
    // Kehre nach Schreiben einer Nachricht hierher zurück
    $_SESSION['return_to'] = $_GET['url'];
    
    $this->render();
  }
  
  /**
   * Action: Eigene Nutzerdaten bearbeiten
   */
  function do_edit() {
    if (empty($_POST)) {
      $this->vars['title'] = 'Eigenes Nutzerkonto bearbeiten';
      
      $_POST = (array)$_SESSION['user'];
    }
    else {
      if ($this->save()) {
        $this->message('Ihre Änderungen wurden gespeichert');
        $user_id = $_SESSION['user']->id;
        $_SESSION['user'] = (object)$_POST;
        $_SESSION['user']->id = $user_id;
        $_SESSION['user']->guest = FALSE;
        $this->redirect($this->user_link(NULL, /*url_only*/TRUE));
      }
    }
    
    $this->render();
  }
  
  function do_index() {
    $this->vars['title'] = 'Nachbarn';

    $sql = "SELECT COUNT(*) FROM users WHERE active = 1 AND password != ''";
    $rs = mysql_query($sql);
    $counter = mysql_fetch_row($rs);
    
    $page = $this->paginate(USERS_PAGE_SIZE, $counter[0], '/user/index');
        
    $sql = "SELECT * FROM users WHERE active = 1  AND password != '' "
      . 'ORDER BY hausnummer, nachname, vorname LIMIT ' . (($page-1)*USERS_PAGE_SIZE) . ',' . USERS_PAGE_SIZE;

    $this->vars['users'] = array();

    $rs = mysql_query($sql);
    
    while ($users = mysql_fetch_object($rs)) {
      $this->vars['users'][] = $users;
    }
    
    $this->render();
  }
  
  function recover_link($email) {
    $unique = FALSE;
    while (!$unique) {
      $code = base_convert(rand(), 10, 36);

      $sql = "UPDATE users SET "
        . "recover = '{$code}', "
        . 'updated = NOW() '
        . "WHERE mail = '" . mysql_real_escape_string($email) . "'";
      $unique = mysql_query($sql);
    }
    return "http://{$_SERVER['HTTP_HOST']}/user/code/{$code}";
  }

  /**
   * Action: Löse E-Mail zum Code-Login aus
   */
  function do_recover() {
    $this->vars['title'] = 'Kennwort vergessen'; 
    $this->vars['slogan'] = 'neues-kennwort';
    
    if (!empty($_POST)) {
      
      $link = $this->recover_link($_POST['mail']);
      
      mail($_POST['mail'], "[pipinstrasse.de] Ihr Anmelde-Link", 
        "Über den folgenden Link können Sie Ihr Kennwort setzen:\n{$link}"
      );
      
      $this->message('Bitte schauen Sie in Ihre E-Mail');
      $this->redirect('/user/login');
    }
    $this->render();
  }
  
  /**
   * Action: Login mit Code
   */
  function do_code() {
    if (count($this->path) != 3) {
      $this->message('FALSCHE URL');
      $this->redirect();
    }

    $sql = 'SELECT * FROM users WHERE active = 1'
        . " AND recover = '" . mysql_real_escape_string($this->path[2]) . "'";

    $rs = mysql_query($sql);
    if ($_SESSION['user'] = mysql_fetch_object($rs)) {
      
      $sql = "UPDATE users SET "
        . "recover = NULL, "
        . 'updated = NOW() '
        . "WHERE id = '" . mysql_real_escape_string($_SESSION['user']->id) . "'";
      mysql_query($sql);
      
      $this->message('Sie sind jetzt angemeldet. Bitte ändern Sie Ihr Kennwort');
      $this->redirect('/user/edit');
    }
    else {
      $this->message('Falscher oder abgelaufener Code. Bitte versuchen Sie es erneut', 'error');
      $this->redirect('/user/login');
    }
  }
  
  /**
   * Action: Skaliertes Avatar-Bild anzeigen
   */
  function do_avatar() {
    $this->layout = FALSE;
    $this->show_scaled_image(USER_RESOLUTION, 'avatars', /*crop*/TRUE);
  }
  
  /**
   * Hilfsfunktion: Nutzerdaten anlegen oder ändern
   */
  function save() {
    global $config;
    
    // Validierung
    $message = array();
    if (empty($_POST['mail']) || !preg_match(USER_EMAIL_RE, $_POST['mail'])) {
      $message[] = 'Bitte geben Sie eine gültige E-Mail-Adresse an';
    }
    
    if ($this->method == 'do_register') {
      // Bei der Registrierung ist das Kennwort Pflichtfeld
      if (empty($_POST['password']) || empty($_POST['password2']) || $_POST['password'] != $_POST['password2']) {
        $message[] = 'Bitte geben Sie das Kennwort zweimal an';
      }
    }
    else {
      // Sonst wird nur geprüft, ob beide Kennwort gleich
      if (!empty($_POST['password']) && $_POST['password'] != $_POST['password2']) {
        $message[] = 'Bitte geben Sie das Kennwort zweimal an';
      }
    }
    if (empty($_POST['vorname'])) {
      $message[] = 'Bitte geben Sie Ihren Vornamen an';
    }
    if (empty($_POST['nachname'])) {
      $message[] = 'Bitte geben Sie Ihren Nachnamen an';
    }
    if (empty($_POST['hausnummer'])) {
      $message[] = 'Bitte geben Sie Ihre Hausnummer an';
    }
    
    if ($message) {
      $this->message(join('<br>', $message), 'error');
      return FALSE;
    }
    else {
      $rs = mysql_query('SELECT id FROM users WHERE mail = '
        . "'" . mysql_real_escape_string($_POST['mail']) . "'");
        
      $user = mysql_fetch_object($rs);
      if ($user) { 
        $sql = 'UPDATE users SET ';
      }
      else {
        $sql = 'INSERT INTO users SET ';
      }

      if (!empty($_POST['password'])) {
        $sql .= 'password = MD5(' .  "'" . mysql_real_escape_string($_POST['password']) . "'), ";
      }
      
      $slug = $this->slug("{$_POST['hausnummer']}-{$_POST['vorname']}-{$_POST['nachname']}");
      
      $sql .= 
          'slug = ' . "'" . mysql_real_escape_string($slug) . "', "
        . 'mail = ' . "'" . mysql_real_escape_string($_POST['mail']) . "', "
        . 'vorname = ' .  "'" . mysql_real_escape_string($_POST['vorname']) . "', "
        . 'nachname = ' .  "'" . mysql_real_escape_string($_POST['nachname']) . "', "
        . 'hausnummer = ' .  "'" . mysql_real_escape_string($_POST['hausnummer']) . "', "
        . 'bio = ' .  "'" . mysql_real_escape_string($_POST['bio']) . "', "
        . 'created = NOW(), '
        . 'updated = NOW()';

      if ($user) {
        $user_id = $user->id;
        $sql .= " WHERE id = {$user_id}";
      }

      $result = mysql_query($sql);
      if (!$result) {
        $this->message(mysql_error(), 'error');
      }

      if (!$user) {
        $user_id = $this->insert_id();
      }

      $src = $_FILES['avatar']['tmp_name'];
      if (is_uploaded_file($src)) {
        $target = $this->image($user_id, NULL, 'avatars');
        // Speichere Bilder in einer Auflösung von 100x100
        // Erst auf Minimalwerte skalieren, dann beschneiden.
        system("{$config['convert']} {$src} -strip -gravity center -geometry '" . USER_RESOLUTION . "^' -crop " . USER_RESOLUTION . "+0+0 {$target}");
        unlink($src);
      }
      
      return $result;
    }
  }
  
  /**
   * Speichere eine Status-Änderung
   */
  function do_status() {
    if (!empty($_POST['status'])) {
      $sql = "UPDATE users SET "
        . "status = '" . mysql_real_escape_string($_POST['status']) . "' "
        . 'updated = NOW() '
        . "WHERE id = {$_SERVER['user']->id}";
      mysql_query($sql);
      
      $this->log('status', NULL, $_POST['status']);
      $this->message('Ihre Statusmeldung wurde gespeichert');
    }
    $this->redirect('/');
  }
  
  /**
   * Erlaube, Externe über Ihre EMail-Adresse einzuladen
   */
  function do_invite() {
    $this->vars['title'] = 'Nachbarn einladen';
    
    $my_name = "{$_SESSION['user']->vorname} {$_SESSION['user']->nachname}";
    
    if (empty($_POST)) {
      $_POST['message'] = "Hallo, 
ich lade dich ein, die neue Webseite unserer Straße auszuprobieren.
Die Inhalte sind nur für angemeldete Nutzer sichtbar.

Du kannst dich über den folgende Link anmelden: 
%LINK%

Liebe Grüße,
{$my_name}";
    }
    else {
      $msg = array();
      $success = TRUE;
      
      $remaining_emails = array();
      
      if (!trim($_POST['emails'])) {
        $this->message('Bitte geben Sie mindestens eine E-Mail-Adresse an', 'error');
      }
      else {
        foreach (explode("\n", trim($_POST['emails'])) as $email) {
          if (preg_match(USER_EMAIL_RE, $email)) {
            $sql = "SELECT * FROM users WHERE mail = '" . mysql_real_escape_string($email) . "'";
            if ($user = mysql_fetch_object(mysql_query($sql))) {
              $msg[] = $this->user_link($user) . ' hat bereits ein Konto hier';
            }
            else {
              /*
               * Generiere aktivierten Nutzer mit temporärer Slug und Code.
               * Die Logik ist grundsätzlich ähnlich zu recover(), nur wird hier der Code
               * gleich beim INSERT und für beide Felder slug und recover generiert.
               */ 
              $unique = FALSE;
              while (!$unique) {
                $code = base_convert(rand(), 10, 36);

                $sql = "INSERT INTO users SET "
                  . " mail = '" . mysql_real_escape_string($email) . "', "
                  . " slug = '" . mysql_real_escape_string($code) . "', "
                  . " recover = '" . mysql_real_escape_string($code) . "', "
                  . " invited_by = {$_SESSION['user']->id}, "
                  . " active = 1, "
                  . " created = NOW(), "
                  . " invited = NOW()";

                $unique = mysql_query($sql);
              }
            
              $link = "http://{$_SERVER['HTTP_HOST']}/user/code/{$code}";
            
              mail($email, "[pipinstrasse.de] Einladung von {$my_name}", 
                strtr($_POST['message'], array('%LINK%' => $link))
              );
            
              $msg[] = "{$email} hat Ihre Einladung erhalten";
            }
          }
          else {
            $remaining_emails[] = $email;
            $msg[] = "{$email} ist keine gültige E-Mail-Adresse";
            $success = FALSE;
          }
        } 
        if ($msg) {
          $this->message(join('<br />', $msg), $success ? 'status' : 'error');
        }

        if ($remaining_emails) {
          $_POST['emails'] = join("\n", $remaining_emails);
        }
        else {
          $this->redirect('/');
        }
      } // Alle Email-Adressen
    }
    $this->render();
  }
  
  /**
   * Lade Gäste in dieses Album ein
   */
  function do_guests() {
    if (count($this->path) != 3) {
      $this->message('FALSCHE URL');
      $this->redirect();
    }
    $topic_id = intval($this->path[2]);

    $sql = "SELECT * FROM topics WHERE "
      . "id = {$topic_id}";

    $topic = mysql_fetch_object(mysql_query($sql));
    if (!$topic) {
      $this->message('FALSCHE URL');
      $this->redirect();
    }

    if ($topic->von != $_SESSION['user']->id) {
      $this->message('Dieses Album gehört Ihnen nicht');
      $this->redirect();
    }

    $this->vars['title'] = "Gäste zu &laquo;{$topic->title}&raquo; einladen";

    $my_name = "{$_SESSION['user']->vorname} {$_SESSION['user']->nachname}";

    $this->vars['intro'] = "Hier können Sie Gäste in Ihr Album &laquo;{$topic->title}&raquo; einladen. Diese können sich das Album ansehen, ohne sich erst auf dieser Web-Seite anmelden zu müssen. Ihre Gäste haben allerdings nur Zugang zu diesem einen Album.";

    if (empty($_POST)) {
      $_POST['message'] = "Hallo, 
ich lade dich zu meinem Fotoalbum &laquo;{$topic->title}&raquo; auf {$_SERVER['HTTP_HOST']} ein.

Du kannst dir das Album über den folgende Link anschauen: 
%LINK%

Liebe Grüße,
{$my_name}";
    }
    else {
      // vgl. user.do_invite()

      $sql = "SELECT code FROM invitations WHERE object_type = 'topic' AND object_id = {$topic->id} AND von = {$_SESSION['user']->id}";
      $invite = mysql_fetch_object(mysql_query($sql));
      if ($invite) {
        $code = $invite->code;
      }
      else {
        /*
         * Generiere Einladung.
         * Die Logik ist grundsätzlich ähnlich zu user.recover(), nur wird hier der Code
         * gleich beim INSERT generiert.
         */ 
        $unique = FALSE;
        while (!$unique) {
          $code = base_convert(rand(), 10, 36);

          $sql = "INSERT INTO invitations SET "
            . " von = {$_SESSION['user']->id}, "
            . " code = '" . mysql_real_escape_string($code) . "', "
            . " object_type = 'topic', "
            . " object_id = {$topic->id}, "
            . " created = NOW()";

          $unique = mysql_query($sql);
        }
      }
      $link = "http://{$_SERVER['HTTP_HOST']}/user/guest/{$code}";
      
      $msg = array();
      $success = TRUE;
      $remaining_emails = array();

      if (!trim($_POST['emails'])) {
        $this->message('Bitte geben Sie mindestens eine E-Mail-Adresse an', 'error');
      }
      else {
        foreach (explode("\n", trim($_POST['emails'])) as $email) {
          if (preg_match(USER_EMAIL_RE, $email)) {
            $sql = "SELECT * FROM users WHERE mail = '" . mysql_real_escape_string($email) . "'";
            if ($user = mysql_fetch_object(mysql_query($sql))) {
              $msg[] = $this->user_link($user) . ' hat bereits ein Konto hier';
            }
            else {

              mail($email, "[pipinstrasse.de] Einladung von {$my_name}", 
                strtr($_POST['message'], array('%LINK%' => $link))
              );

              $msg[] = "{$email} hat Ihre Einladung erhalten";
            }
          }
          else {
            $remaining_emails[] = $email;
            $msg[] = "{$email} ist keine gültige E-Mail-Adresse";
            $success = FALSE;
          }
        } 
        if ($msg) {
          $this->message(join('<br />', $msg), $success ? 'status' : 'error');
        }

        if ($remaining_emails) {
          $_POST['emails'] = join("\n", $remaining_emails);
        }
        else {
          $this->redirect();
        }
      }
    }

    $this->render('user_invite');
  }
  
  /**
   * Zugang auf bestimmte Objekte für nicht angemeldete Gäste
   */
  function do_guest() {
    if (count($this->path) != 3) {
      $this->message('FALSCHE URL');
      $this->redirect();
    }
    $code = intval($this->path[2]);

    $sql = "SELECT * FROM invitations WHERE code = {$code}";
    $invite = mysql_fetch_object(mysql_query($sql));
    if (!$invite) {
      $this->message('Ungültige Einladung');
      $this->redirect('/');
    }
    $invite->guest = TRUE;
    
    switch ($invite->object_type) {
      case 'topic':    
        $sql = "SELECT * FROM photos WHERE topic_id = {$invite->object_id} LIMIT 1";
        $photo = mysql_fetch_object(mysql_query($sql));

        $_SESSION['user'] = $invite;
        $this->message('Sie haben eingeschränkten Zugang');
        $this->redirect("/photo/view/{$photo->id}");
        break;

      default:
        $this->message('FALSCHE URL');
        $this->redirect();
    }
  }
  
  /**
   * Alle Methoden sind erlaubt
   */
  function allowed() {
    return TRUE;
  }
}

