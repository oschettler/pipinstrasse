<?php

class model {

  function insert_id() {
    global $db;
    $insert_id = mysqli_fetch_row(mysqli_query($db, 'SELECT LAST_INSERT_ID()'));
    return $insert_id[0];
  }
  
  function query($sql) {
    global $db;
    $result = array();
    $rs = mysqli_query($db, $sql);
    if (!$rs) {
      error_log('QUERY() ERROR: ' . mysqli_error($db));
      return NULL;
    }
    while ($_ = mysqli_fetch_object($rs)) {
      $result[] = $_;
    }
    return $result;
  }
  
  function exec($sql) {
    global $db;
    $result = mysqli_query($db, $sql);
    if (!$result) {
      error_log('EXEC() ERROR: ' . mysqli_error($db));
    }
    return $result;
  }
  
  function one($sql) {
    global $db;
    $rs = mysqli_query($db, $sql);
    if (!$rs) {
      error_log('ONE() ERROR: ' . mysqli_error($db));
      return NULL;
    }
    return mysqli_fetch_object($rs);
  }
  
  function count($sql) {
    global $db;
    $rs = mysqli_query($db, $sql);
    if (!$rs) {
      error_log('COUNT() ERROR: ' . mysqli_error($db));
      return NULL;
    }
    $counter = mysqli_fetch_row($rs);
    return $counter[0];
  }
  
  /**
   * Static to be called without an instance
   */
  static function slug($str) { 
    return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), self::remove_accent($str)));   
  }

  static function remove_accent($str) {
    $a = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','Ā','ā','Ă','ă','Ą','ą','Ć','ć','Ĉ','ĉ','Ċ','ċ','Č','č','Ď','ď','Đ','đ','Ē','ē','Ĕ','ĕ','Ė','ė','Ę','ę','Ě','ě','Ĝ','ĝ','Ğ','ğ','Ġ','ġ','Ģ','ģ','Ĥ','ĥ','Ħ','ħ','Ĩ','ĩ','Ī','ī','Ĭ','ĭ','Į','į','İ','ı','Ĳ','ĳ','Ĵ','ĵ','Ķ','ķ','Ĺ','ĺ','Ļ','ļ','Ľ','ľ','Ŀ','ŀ','Ł','ł','Ń','ń','Ņ','ņ','Ň','ň','ŉ','Ō','ō','Ŏ','ŏ','Ő','ő','Œ','œ','Ŕ','ŕ','Ŗ','ŗ','Ř','ř','Ś','ś','Ŝ','ŝ','Ş','ş','Š','š','Ţ','ţ','Ť','ť','Ŧ','ŧ','Ũ','ũ','Ū','ū','Ŭ','ŭ','Ů','ů','Ű','ű','Ų','ų','Ŵ','ŵ','Ŷ','ŷ','Ÿ','Ź','ź','Ż','ż','Ž','ž','ſ','ƒ','Ơ','ơ','Ư','ư','Ǎ','ǎ','Ǐ','ǐ','Ǒ','ǒ','Ǔ','ǔ','Ǖ','ǖ','Ǘ','ǘ','Ǚ','ǚ','Ǜ','ǜ','Ǻ','ǻ','Ǽ','ǽ','Ǿ','ǿ'); 
    $b = array('A','A','A','A','Ae','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','Oe','O','O','U','U','U','Ue','Y','ss','a','a','a','a','ae','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','oe','o','u','u','u','ue','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o'); 
    return str_replace($a, $b, $str); 
  }
  
  function name() {
    return get_class($this);
  }
  
  function table() {
    return $this->name() . 's';
  }
  
  function update_comment_count($id, $n) {
    $sql = "UPDATE " . $this->table() . " SET comment_count = {$n} WHERE id = {$id}";
    return $this->exec($sql);
  }
}
