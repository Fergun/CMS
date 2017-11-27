<?php

abstract class DatabaseGlobal
{
  protected function encoding($text, $revert = 0){
    if($GLOBALS['db_encoding'] && $GLOBALS['db_encoding'] != $GLOBALS['charset'] && !empty($GLOBALS['charset'])){
      return ($revert==0 ? iconv($GLOBALS['db_encoding'],$GLOBALS['charset'], $text) : iconv($GLOBALS['charset'],$GLOBALS['db_encoding'], $text));
    }else{
      return $text;
    } 
  }

  protected function showErrorMessage($errorInfo = array()){
    echo '<b style="color: red;">Message: </b><b>' . $errorInfo['msg'] . '</b><br>';
    echo '<b style="color: red;">Error number: </b><b>[' . $errorInfo['errno'] . '] </b><b style="color: red;">Error info: </b><b>' . $errorInfo['error'] . '</b><br>';
    echo '<b style="color: red;">File info: </b><b>Error in file ['.$errorInfo['file'].'] on line ['.$errorInfo['line'].']. Thrown by function ['.$errorInfo['function'].']</b><br><br>';
  }
}