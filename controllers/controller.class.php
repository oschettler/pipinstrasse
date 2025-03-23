<?php
/***********************************************************************************
Copyright (c) 2011 Olav Schettler <olav@schettler.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
----

This is the MIT Open Source License of http://www.opensource.org/licenses/MIT
***********************************************************************************/

define('COMMENT_PAGE_SIZE', 10);
require_once 'model.class.php';

/**
 * Oberklasse für alle Controller.
 * Definiert nützliche Hilfsfunktionen.
 * Action-Methoden werden in Unterklassen als do_...() definiert.
 */
class controller {
  var $uses = array(); // Modelle
  var $vars = array(); // View-Variable
  var $layout = TRUE;
  
  /**
   * Füge Modelle früh hinzu so dass sie von allowed() genutzt werden können
   */
  function __construct() {
    // user-, message-, photo- und stream-Modelle werden für Blöcke in jedem Fall eingebunden
    foreach (array('user', 'message', 'photo', 'stream') as $model) {
      if (!in_array($model, $this->uses)) {
        $this->uses[] = $model;
      }
    }
    foreach ($this->uses as $model_name) {
      $this->model($model_name);
    }
  }
  
  /**
   * Callback. Erlaubt die Manipulation der aufgerufenen Methode in Unterklassen
   */
  function method() {
    return $this->path[1];
  }

  /**
   * Callback. Wird in Index vor Aufruf der eigentlichen Action aufgerufen
   * und kann Controller-übergreifende Logik enthalten
   */
  function before() {
    global $config;
    
    $this->vars['title'] = ucfirst($this->name);
    $this->vars['slogan'] = 'Zu Hause in der Bonner Altstadt';
    $this->vars['theme'] = $this->theme;
    $this->vars['navigation'] = $config['navigation'];
    $this->vars['page_head'] = array();
  }
  
  /**
   * Setze Modell als Instanzvariable ein
   */
  function model($model_name) {
    require_once "{$model_name}.php";
    $this->$model_name = new $model_name;
  }
  
  /**
   * Callback
   */
  function before_layout() {
    if (empty($_SESSION['user']) || $_SESSION['user']->guest) {
      return;
    }

    $this->vars['message_count'] = $this->message_count();

    $this->vars['blocks']['sidebar2'][] = $this->block(array(
      'name' => 'users-online',
      'title' => 'Online',
      'view' => '_online'
    ));

    $random_user = $this->random_user(); 
    if ($random_user) {
      $this->vars['blocks']['sidebar2'][] = $this->block(array(
        'name' => 'random-user',
        'title' => '<span>Kurz vorgestellt:</span> <a href="' 
          . $this->user_link($random_user, /*url_only*/TRUE) . '">'
          . "{$random_user->vorname} {$random_user->nachname}</a>",
        'view' => '_random_user'
      ), array(
        'random_user' => $random_user,
      ));
    }
   
    if ($this->name != 'photo') {
      $this->vars['blocks']['sidebar1'][] = $this->block(array(
        'name' => 'random-photo',
        'title' => 'Foto aus der Nachbarschaft',
        'view' => '_random_photo',
      ));
    }
  }
  
  /**
   * Gibt ein Template aus
   * Optionale Variable überschreiben gleichnamige Controller-Variable
   */
  function render($template = NULL, $vars = array()) {
    global $config;

    // render() ohne Parameter setzt Standardname für Template
    if ($template == NULL) {
      $template = "{$this->name}_{$this->method}";
    }
    extract($this->vars);
    extract($vars);
    require "{$template}.php";
  }
  
  /**
   * Rendere einen Block als Objekt
   */
  function block($block, $vars = array()) {
    $block = (object)$block;
    ob_start();
    $this->render($block->view, $vars);
    $block->contents = ob_get_clean();
    return $block;
  }
  
  /**
   * Rendere eine Region mit Blöcken
   */ 
  function blocks($region) {
    ?>
    <ul class="blocks" id="blocks-<?php echo $region; ?>">
      <?php 
      if (!empty($this->vars['blocks'][$region])) {
        foreach ($this->vars['blocks'][$region] as $block) {
          ?>
          <li class="block" id="<?php echo $block->name; ?>">
          <h2><?php echo $block->title; ?></h2>
          <?php echo $block->contents; ?>
          </li>
          <?php
        }
      }
      ?>
    </ul>
    <?php
  } 
  
  function redirect($url = NULL) {
    if ($url == NULL) {
      if (!empty($_SESSION['return_to']) && $_SESSION['return_to'] != '/chat/users') {
        $url = $_SESSION['return_to'];
        unset($_SESSION['return_to']);
      }
      else {
        $url = '/';
      }
    }
    header ("Location: {$url}");
    exit;
  }
  
  function message($text = NULL, $class = 'status') {
    if ($text == NULL) {
      $message = empty($_SESSION['message']) 
        ? array() 
        : $_SESSION['message'];
      unset($_SESSION['message']);
    }
    else {
      $_SESSION['message'] = $message = array(
        'text' => $text,
        'class' => $class,
      );
    }
    return $message;
  }
  
  /**
   * Erzeuge einen Link <a href=""></a> für einen Nutzer, falls vorhanden mit Avatar-Bild
   */
  function user_link($user = NULL, $url_only = FALSE, $image = TRUE) {
    if ($user == NULL) {
      $user = $_SESSION['user'];
    }
    
    $slug = $this->slug("{$user->hausnummer}-{$user->vorname}-{$user->nachname}");
    $url = "/user/view/{$slug}";  
        
    if ($url_only) {
      return $url;
    }
    else {
      if ($image) {
        $img = '<img class="avatar" src="' . $this->user_avatar($user) . '" />';        
      }
      else {
        $img = '';
      }
      
      return "{$img}<a p:id=\"{$user->id}\" href=\"{$url}\" title=\"Hausnummer {$user->hausnummer}\">{$user->vorname} {$user->nachname}</a>";
    }
  }
  
  function user_avatar($user, $geo = '20x20') {
    $user_id = !empty($user->u_id) ? $user->u_id : $user->id;
    if (file_exists($this->image($user_id, NULL, 'avatars'))) {
      return "/user/avatar/{$user_id}/{$geo}";
    }
    else {
      return "/user/avatar/0/{$geo}";
    }
  }
  
  function remove_accent($str) {
    return model::remove_accent($str);
  }

  static function slug($str) { 
    return model::slug($str);
  }
  
  /**
   * Bereite die Variablen für die Paginierung vor und schreibe sie in $this->vars['paginate'].
   * Liest die aktuelle Seite aus $this->path[2]
   * @param $size Anzahl Einträge pro Seite
   * @param $count Gesamtzahl von Einträgen
   * @param $url URL-Prefix 
   * @param $url_part URL-Komponente mit Seitennummer. /board/index/3 => 2; /photo/view/9/3 => 3
   */
  function paginate($size, $count, $url, $url_part = 2) {
    if (count($this->path) == $url_part) {
      $page = 1;
    }
    else {
      $page = intval($this->path[$url_part]);
    }
    
    $paginate = array(
      'page' => $page,
      'size' => $size,
      'count' => $count,
      'page_count' => intval(ceil($count / $size)),
      'links' => array(),
    );
    
    if ($page > 1) {
      $paginate['prev'] = '<a href="' . $url . ($page == 2 ? '' : ('/' . ($page-1))) . '">&laquo; Seite zurück</a>';
      $paginate['links'][] = $paginate['prev'];
    }
    
    foreach (range(max(1, $page-2), min($page+2, $paginate['page_count'])) as $n) {
      if ($n == $page) {
        $paginate['links'][] = "<span class=\"current\">{$n}</span>";
      }
      else {
        $paginate['links'][] = "<a href=\"{$url}/{$n}\">{$n}</a>";
      }
    }
    
    if ($page < $paginate['page_count']) {
      $paginate['next'] = "<a href=\"{$url}/" . ($page+1) . '">Seite vor &raquo;</a>';
      $paginate['links'][] = $paginate['next'];
    }
    
    // Links komplett ausblenden, wenn nur eine einzige Seite
    if (1 == count($paginate['links'])) {
      $paginate['links'] = array();
    }
    
    $this->vars['paginate'] = $paginate;
    return $page;
  }

  function message_count() {
    global $db;
    if (empty($_SESSION['user'])) {
      return 0;
    }
    $sql = 'SELECT COUNT(*) FROM messages WHERE '
      . "an = '" . mysqli_real_escape_string($db, $_SESSION['user']->id) . "' "
      . "AND viewed IS NULL";

    return $this->message->count($sql);
  }
  
  static function image_path($id, $type) {
    $dirs = array();
    $path = sprintf('%06s', $id);
    while ($path) {
      array_unshift($dirs, substr($path, 0, 2)); // add to start
      $path = substr($path, 2);
    }
    return '/img/' . $type . '/' . join('/', $dirs);
  }
  
  static function image($id, $geo = NULL, $type = 'photos') {
    $image_path = $_SERVER['DOCUMENT_ROOT'] . self::image_path($id, $type);
    @mkdir($image_path, 0775, /*recursive*/TRUE);

    if ($geo == NULL) {
      return sprintf("{$image_path}/%05d.jpg", $id);
    }
    else {
      return sprintf("{$image_path}/%05d_%s.jpg", $id, $geo);
    }
  }
  
  /**
   * Mache einen Eintrag im Action Stream
   */
  function log($type, $id, $title = '(none)') {
    global $db;
    if (strlen($title) > 80) {
      // Einige Zeichen weniger als 100, damit Backslashes noch hinein passen
      $title = substr($title, 0, 80) . '...';
    }
    
    $sql = 'INSERT INTO stream SET '
      . 'von = ' .  "'" . mysqli_real_escape_string($db, $_SESSION['user']->id) . "', "
      . 'title = ' .  "'" . mysqli_real_escape_string($db, $title) . "', "
      . 'object_type = ' .  "'" . mysqli_real_escape_string($db, $type) . "', "
      . 'object_id = ' .  "'" . mysqli_real_escape_string($db, $id) . "', "
      . 'created = NOW()';

    $result = $this->stream->exec($sql);
  }
  
  /**
   * Gib die übergebene Zeit relativ zu jetzt aus
   */
  function reltime($datetime) {
    $diff = time() - strtotime($datetime);

    if ($diff < 60) {
      return 'gerade';
    }
    if ($diff < 3600) {
      $mins = intval($diff / 60);
      return 'vor ' . ($mins == 1 ? 'einer Minute' : "{$mins} Minuten");  
    }
    if ($diff < 86400) {
      $hours = intval($diff / 3600);
      return 'vor ' . ($hours == 1 ? 'einer Stunde' : "{$hours} Stunden");  
    }
    return strftime('am %d.%m.%Y um %H:%M Uhr', strtotime($datetime));
  }
  
  /**
   * Gibt je nach Wert von $n korrekte Pluralform zurück
   * Aufruf mit einem Formatparameter: Ersetzt %d
   * Aufruf mit zwei Formatparametern: 
   * - bei == 1 ergibt zweiten Parameter, 
   * - sonst %d in erstem Parameter
   */
  function plural($n) {
    $params = func_get_args();
    if ($n == 1 && count($params) == 3) {
      return $params[2];
    }
    else {
      return sprintf($params[1], $n);
    }
  }
  
  function show_scaled_image($default_resolution, $type, $crop = FALSE) {
    global $config;
     
    if (count($this->path) < 3) {
      $this->message('FALSCHE URL');
      $this->redirect();
    }
    
    $id = $this->path[2];
    if (count($this->path) == 3) {
      $geo = $default_resolution;
    }
    else {
      $geo = $this->path[3];
    }

    header('Pragma: no-cache');
    header('Cache-control: no-cache');
    header('Expires: 0');

    if ($geo == $default_resolution) {
      // Nimm das Original
      $scaled = $this->image($id, NULL, $type);
    }
    else {
      $scaled = $this->image($id, $geo, $type);
    }

    if (!file_exists($scaled)) {
      $geo = preg_replace('/\W+/', '', $geo);
      $base = $this->image($id, NULL, $type);
      if (file_exists($base)) {
        $scaled = $this->image($id, $geo, $type);
        
        // Bei Fotos soll nur die kleine Auflösung mit crop=TRUE skaliert werden
        if ($type == 'photos' && $geo == '100x100') {
          $crop = TRUE;
        }
        
        if ($crop) {
          system("{$config['convert']} {$base} -strip -gravity center -geometry '{$geo}^' -crop {$geo}+0+0 {$scaled}");
        }
        else {
          system("{$config['convert']} {$base} -strip -geometry {$geo} {$scaled}");
        }
      }
      else {
        return "Keine Datei {$base}";
      }
    } 

    $info = getimagesize($scaled);
    header("Content-type: {$info['mime']}");
    readfile($scaled);

    return TRUE;
  }
  
  function online() {
    if (!empty($_SESSION['user'])) {    
      $sql = 'UPDATE users SET '
        . 'online = NOW() '
        . "WHERE id = {$_SESSION['user']->id}";  
      $this->user->exec($sql);
    }
  }
  
  function users_online() {
    if (!empty($_SESSION['user'])) {    
      $except_me = "AND id != {$_SESSION['user']->id} ";
    }
    else {
      $except_me = '';
    }

    $sql = 'SELECT * FROM users WHERE active = 1 '
      . 'AND online >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) '
      . $except_me
      . 'ORDER BY online DESC '
      . 'LIMIT 10';

    return $this->user->query($sql);
  }
  
  /**
   * Wählt einen zufälligen Nutzer aus
   */
  function random_user() {
    if (!empty($_SESSION['user'])) {    
      $except_me = "AND id != {$_SESSION['user']->id} ";
    }
    else {
      $except_me = '';
    }
    
    $sql = "SELECT COUNT(*) FROM users WHERE password != '' AND active = 1 "
      . $except_me;
      
    $count = $this->user->count($sql);

    $index = rand(0, $count-1);

    $sql = 'SELECT * FROM users WHERE active = 1 '
      . $except_me
      . "LIMIT {$index},1";

    return $this->user->one($sql);
  }
  
  /**
   * Wählt eine zufälliges Foto aus
   */
  function random_photo() {
    $sql = 'SELECT COUNT(*) FROM photos';      
    $count = $this->photo->count($sql);

    $index = rand(0, $count-1);

    $sql = 'SELECT * FROM photos '
      . "LIMIT {$index},1";

    return   $this->photo->one($sql);
  }
  
  /**
   * Hole eine Seite Kommentare für ein Object
   */
  function comments($type, $id, $url) {
    $sql = 'SELECT COUNT(*) FROM comments WHERE '
      . "object_type = '{$type}' "
      . "AND object_id = {$id}"; 
    
    $count = $this->comment->count($sql);

    $page = $this->paginate(COMMENT_PAGE_SIZE, $count, $url, /*url_part*/3);

    if ($count == 0) {
      return array();
    }
    
    $sql = 'SELECT c.created as c_created, c.id AS c_id, c.*, u.* FROM comments c LEFT JOIN users u ON c.von = u.id WHERE '
      . "object_type = '{$type}' "
      . "AND object_id = {$id} "
      . 'ORDER BY c.created DESC LIMIT ' . (($page-1)*COMMENT_PAGE_SIZE) . ',' . COMMENT_PAGE_SIZE; 

    return $this->comment->query($sql);
  }
  
  /**
   * Liefert eine Liste verfügbarer Designs
   */
  function themes() {
    $themes = array('---');
    $base = 'themes';
    $d = opendir($base);
    while ($entry = readdir($d)) {
      if (strpos($entry, '.') === 0) {
        continue;
      }
      if (is_dir("{$base}/{$entry}")) {
        $themes[] = $entry;
      }
    }
    closedir($d);
    return $themes;
  }
  
  /**
   * Definiert einen HTML-Block, der im Layout im Seitenkopf eingebunden wird
   */ 
  function page_head() {
    ob_start();
  }

  function end_page_head() {
    $this->vars['page_head'][] = ob_get_clean();
  }
  
  /**
   * Generelles Verhalten: Gäste haben keinen Zugang
   */
  function allowed() {
    return !(empty($_SESSION['user']) || $_SESSION['user']->guest);
  }
  
  /**
   * Formatiere einen Text:
   * - Entferne HTML-Tags
   * - Mache URLs klickbar
   * - Wandle Zeilenumbrüche in <br>
   */
  function format($text) {
    return nl2br(preg_replace('#(http://(\S+))#', '<a href="$1">$2</a>', strip_tags($text)));
  }
  
  /**
   * Erzeuge / prüfe eine einfache Rechnung
   * Wenn Daten und Berechnung korrekt, TRUE
   * sonst Berechnung als Array
   */
  function captcha($data = NULL) {
    $op = array('plus', 'minus', 'mal');
    $last = empty($_SESSION['captcha']) ? array() : $_SESSION['captcha'];

    // Vermeide a==b, da das zu result==0 führt 
    do {
      $a = rand(1, 10);
      $b = rand(1, 10);
    }
    while ($a == $b);

    // Stelle sicher, dass minus ein positives Ergebnis hat
    if ($a > $b) {
      $captcha['a'] = $a;
      $captcha['b'] = $b;
    }
    else {
      $captcha['a'] = $b;
      $captcha['b'] = $a;
    }
    $captcha['n'] = rand(0, 2);

    $captcha += array(
      'op' => $op,
      'text' => "{$captcha['a']} {$op[$captcha['n']]} {$captcha['b']}",
    );
    $_SESSION['captcha'] = $captcha;  

    if (!empty($data['captcha'])) {
      $chk = intval($data['captcha']);
      //D echo 'Aufgabe: ' . $last['a'] . $op[$last['n']] . $last['b'] . ' = ' . $chk . "\n";

      switch ($last['n']) {
        case 0:
          if ($chk == $last['a'] + $last['b']) {
            return TRUE;
          }
          break;

        case 1:
          if ($chk == $last['a'] - $last['b']) {
            return TRUE;
          }
          break;

        case 2:
          if ($chk == $last['a'] * $last['b']) {
            return TRUE;
          }
          break;
      }
    }    
    return $captcha;
  }
}
