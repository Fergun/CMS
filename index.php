<?php
require('support/init.php');
$title = 'Adam Mikołajczyk';
require('support/style.php');

require('system/System_Init.php');
require('system/Config.php');
require('system/Process.php');


echo '<body name="index">';
$cont_in = '<div class="content"><div class="container">';
$cont_out = '</div></div>';
// Menu dla dokumentu
//
echo $cont_in;
echo '<form>';
//Wyszukiwarka
echo '<br><div class="div-table"><div class="div-header-row"><div class="div-cell nopadding border center">';
echo '<span '.tooltip('Undertheowl').' class="actions text">System CMS</span>';
echo '<span '.tooltip('Wyszukaj proces').' class="actions return"><input name="search" class="search" value="'.$GLOBALS['search'].'"></span>';
echo '<span '.tooltip('Dodaj').' class="glyphicon glyphicon-plus actions main-actions" onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?mode=create&header_code=headers\'"></span>';
if(isset($GLOBALS['search'])){
    echo '<span '.tooltip('Zamknij').' class="glyphicon glyphicon-remove actions return" onclick="window.location.href=\'http://undertheowl.pl/cms\'"></span>';
}
echo '</div></div></div><br>';
echo '</form>';


//Załadowanie skrótow do AKTUALNOóCI
echo '<div class="div-table">';
$config = new Config();
$system = new System_Init($db,$config);
$processList = $system->getProcessesList($GLOBALS['search']);
foreach ($processList as $oneProcess) {
    $process = $system->getProcess($oneProcess['code']);
    echo '<nav class="div-context-menu '.$oneProcess['code'].'">';
    foreach ($process['actions'] as $action){
        echo '<nav class="context-menu-list">'. $action['name'] .'</nav>';
    }
    echo '</nav>';
    echo '<div class="div-row '.$oneProcess['code'].'">';
    echo '<div '.tooltip('Status procesu: '. ($process['status']['name'] ? $process['status']['name'] : '(brak)' )).' class="div-cell border center" onclick="window.location.href=\'http://undertheowl.pl/cms/view.php?header_code='. $oneProcess['code'] .'\'">'. $oneProcess['name'] .'</div>';
    echo '</div>';
    unset($process);
}
echo '</div>';

unset($system);
unset($config);
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
