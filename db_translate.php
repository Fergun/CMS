<?php

function my_translate($Query_String){
    $Query_String = str_replace('\"', '-psiakrew-', $Query_String);
    $Query_String = str_replace("\'", '-psiakrew-', $Query_String);
    $Query_String = str_replace("'", "\'", $Query_String);
    $Query_String = str_replace('"', "'", $Query_String);
    $Query_String = str_replace('-psiakrew-', '"', $Query_String);
    return $Query_String;

}