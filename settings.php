<?php
//Stara
$db_host          = 'mysql516int.cp.az.pl';
$db_database      = 'db1222674_26543';
$db_user          = 'u1222674_26543';
$db_password      = 'p4D,rucH4^COh91s';
//Nowa
$db_host          = 'mysql597int.cp.az.pl';
$db_database      = 'db1222674_test';
$db_user          = 'u1222674_test';
$db_password      = 'sTmFbP6S';


include('class.db_mysql.inc.php'); // default

$db = new db;
$db->Host       = $db_host;
$db->Database   = $db_database;
$db->User       = $db_user;
$db->Password   = $db_password;

$color='red';
if (!$db->connect_errno) {
    $color='green';
}
$db->query("SET CHARSET utf8");
$db->query("SET NAMES `utf8` COLLATE `utf8_polish_ci`");
