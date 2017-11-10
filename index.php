<?php
echo '<html><head>';
echo '<title>Adam Mikołajczyk</title>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">';
echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>';
echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
echo '<link href="https://fonts.googleapis.com/css?family=PT+Sans+Narrow" rel="stylesheet">';
echo '<link rel="stylesheet" href="style.css" type="text/css">';
echo '</head>';
echo '<body name="index">';
require('settings.php');
require('functions_view.php');
$cont_in = '<div class="content"><div class="container">';
$cont_out = '</div></div>';
// Menu dla dokumentu
echo '<nav class="div-context-menu">';
echo '<nav class="context-menu-list">Modyfikacje</nav><nav class="context-menu-list">Zakończ</nav>';
echo '</nav>';
//
echo $cont_in;

//Wyszukiwarka
echo '<br><div class="div-table"><div class="div-header-row"><div class="div-cell nopadding border center">';
echo '<span '.tooltip('Undertheowl').' class="actions text">System CMS do zarządzania stroną</span>';
//echo '<span '.tooltip('Szukaj').' class="actions return"><input name="search" class="search" value="'.$GLOBALS['search'].'"></span>';
//echo '<span '.tooltip('Dodaj').' class="glyphicon glyphicon-plus actions main-actions" onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?mode=create&header_code='.$header_code.'\'"></span>';
echo '</div></div></div><br>';



//Załadowanie skrótow do AKTUALNOóCI
echo '<div class="div-table">';
$db->query('Select * from uto_headers');
while($db->next_record())
{
    echo '<div class="div-row">';
    echo '<div class="div-cell border center" onclick="window.location.href=\'http://undertheowl.pl/cms/view.php?header_code='. $db->f('uh_code') .'\'">'. $db->f('uh_name') .'</div>';
    echo '</div>';
}
echo '</div>';


echo $cont_out;

echo '<script>
$(document).ready(function(){
    $(\'[data-toggle="tooltip"]\').tooltip();   
});
</script>
<script src="context-menu.js"> </script>';
echo '</body>';
echo '</html>';
?>
