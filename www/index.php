<?php
/***********************************************************************************
Copyright (c) 2011 Olav Schettler <olav@schettler.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
----

This is the MIT Open Source License of http://www.opensource.org/licenses/MIT
***********************************************************************************/
session_start();
require_once '../config.php';

/*
 * Debugging. Aktivieren mit Cookie "XDEBUG_SESSION", 
 * z.B. per Safari-Extension Xdebug-Toggler
 */
if (isset($_COOKIE['XDEBUG_SESSION']) || !empty($_GET['t'])) {
  error_reporting(E_ALL);
  ini_set('display_errors', TRUE);
  require_once '../lib/krumo/class.krumo.php';
}

if (!empty($_SESSION['theme'])) {
  $theme = $_SESSION['theme'];
}
else
if (!empty($config['theme'])) {
  $theme = $config['theme'];
}
else {
  $theme = NULL;
}

$include_path = '.' 
  . ':' . realpath("{$_SERVER['DOCUMENT_ROOT']}/{$config['dir_models']}")
  . ':' . realpath("{$_SERVER['DOCUMENT_ROOT']}/{$config['dir_controllers']}");

/*
 * Theming: Das Verzeichnis mit den Theme-Dateien steht im Include-Pfad vor 
 * Den normalen View-Dateien. Theme-Dateien werden so bevorzugt geladen.
 */
if ($theme) {
  $include_path .= 
    ':' . realpath("{$_SERVER['DOCUMENT_ROOT']}/themes/{$theme}/templates/");
}    

             
$include_path .=
  ':' . realpath("{$_SERVER['DOCUMENT_ROOT']}/{$config['dir_views']}");
  
ini_set('include_path', $include_path);
//D echo ini_get('include_path'); exit;

require_once "controller.class.php";

if ($_SERVER['REQUEST_URI'] == '/') {
  $_SERVER['REQUEST_URI'] = '/home';
}

if (empty($_SESSION['user'])) {
  $allowed = FALSE;
  foreach (array(
      '/user/login', 
      '/user/register', 
      '/user/recover',
      '/user/code', 
      '/user/guest', 
      '/page',
      '/message/contact',
      '/home/theme',
    ) as $url) {

    if (preg_match("#^{$url}(/.*)?$#", $_SERVER['REQUEST_URI'])) {
      $allowed = TRUE;
      break;
    }
  }

  if (!$allowed) {
    $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    $_SESSION['message'] = array(
      'text' => 'Die Inhalte auf diesen Seiten sind nur angemeldet zu sehen.<br>Bitte melden Sie sich an oder besuchen Sie unsere <a href="/page/intro">öffentlichen Seiten</a>.',
      'class' => 'error',
    );
    header('Location: /user/login');
    exit;
  }
}

$db = mysqli_connect($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);
mysqli_query($db, 'SET NAMES UTF8');
//mysqli_query($db, 'SET CHARACTER SET UTF8');

$path = explode('/', substr($_SERVER['REQUEST_URI'], 1));

if (count($path) < 2) {
  $path[1] = 'index';
}

// Sonderzeichen aus ersten Pfadkomponenten herausschmeissen
foreach (array(0,1) as $i) {
  if (!empty($path[$i])) {
    $path[$i] = preg_replace('/\W+/', '_', $path[$i]);
  }
}

/*
 * Aus den ersten beiden Pfadkomponenten wird eine Datei mit Controller-Klasse
 * und eine Methode ermittelt
 */
$file = "{$config['dir_controllers']}{$path[0]}_controller.php";
if (file_exists($file)) {
  include $file;
  $controller_name = "{$path[0]}_controller";

  if (class_exists($controller_name)) {
    $controller = new $controller_name;
    $controller->path = $path;
    $controller->name = $path[0];
    $controller->theme = $theme;

    $controller->method = $controller->method();
    $method = "do_{$controller->method}";

    if (!$controller->allowed()) {
      $controller->redirect();
    }

    $controller->before(); 

    if (method_exists($controller, $method)) {
      
      // Track online status
      $controller->online();

      ob_start();
      $controller->{$method}();
      $controller->vars['contents'] = ob_get_clean();

      if ($controller->layout) {
        $controller->before_layout();
        $controller->render($controller->layout === TRUE ? 'layout' : "{$controller->layout}_layout");
      }
      else {
        echo $controller->vars['contents'];
      }
      exit;
    }
  }
}

echo "FALSCHE URL " . join('/', $path);
//print $_GET['url'];
#var_dump($_SERVER['PATH_INFO']);     
