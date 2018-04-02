<?php
foreach ($_GET as $key => $val) $GLOBALS[$key] = $val;
foreach ($_POST as $key => $val) $GLOBALS[$key] = $val;
foreach ($_COOKIE as $key => $val) $GLOBALS[$key] = $val;
foreach ($_REQUEST as $key => $val) $GLOBALS[$key] = $val;

require('settings.php');
require('functions_view.php');
require('system_functions.php');
