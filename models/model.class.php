<?php

class model {

  function insert_id() {
    $insert_id = mysql_fetch_row(mysql_query('SELECT LAST_INSERT_ID()'));
    return $insert_id[0];
  }
  
  function query($sql) {
    $result = array();
    $rs = mysql_query($sql);
    if (!$rs) {
      error_log('QUERY() ERROR: ' . mysql_error());
      return NULL;
    }
    while ($_ = mysql_fetch_object($rs)) {
      $result[] = $_;
    }
    return $result;
  }
  
  function exec($sql) {
    $result = mysql_query($sql);
    if (!$result) {
      error_log('EXEC() ERROR: ' . mysql_error());
    }
    return $result;
  }
  
  function one($sql) {
    $rs = mysql_query($sql);
    if (!$rs) {
      error_log('ONE() ERROR: ' . mysql_error());
      return NULL;
    }
    return mysql_fetch_object($rs);
  }
  
  function count($sql) {
    $rs = mysql_query($sql);
    if (!$rs) {
      error_log('COUNT() ERROR: ' . mysql_error());
      return NULL;
    }
    $counter = mysql_fetch_row($rs);
    return $counter[0];
  }
}
