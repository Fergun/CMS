<?php

require('support/init.php');

require('system/Config.php');
require('system/Process.php');
$config = new Config();
$indexes = $config->getData()['indexes'];
$process = new Process($config->getData(),$GLOBALS['header_code']);
//Załadowanie nagłówków
$headers = $process->getProcessHeaders();

if($GLOBALS['order']) {
    $order = $GLOBALS['order'];
    if ($GLOBALS['desc'])
        $order .= ' desc';
}

//Załadowanie dokumentów
$documents = $process->getDocuments($headers, $GLOBALS['search'], $GLOBALS['filters'], $order);

$title = $process->getProcessName();
require('support/style.php');

echo '<body name="'.$GLOBALS['header_code'].'">';

//Start strony
echo '<div class="content"><div class="container">';

echo '<br>';
echo '<form name="view" id="view" method="post">';
send_hidden('header_code',$header_code);
send_hidden('clear','');
send_hidden('order','');
send_hidden('desc',0);


echo '<div class="div-table">';
//Wyszukiwarka
echo '<div class="div-header-row"><div class="div-cell nopadding border search">';
echo '<span '.tooltip('Powrót').' class="glyphicon glyphicon-arrow-left actions return" onclick="window.location.href=\'http://undertheowl.pl/cms\'"></span>';
echo '<span '.tooltip('Szukaj').' class="actions return"><input name="search" class="search" value="'.$GLOBALS['search'].'"></span>';
echo '<span '.tooltip('Dodaj').' class="glyphicon glyphicon-plus actions main-actions" onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?mode=create&header_code='.$header_code.'\'"></span>';
if(isset($GLOBALS['search']) && $GLOBALS['search']){
    echo '<span '.tooltip('Zamknij').' class="glyphicon glyphicon-remove actions return" onclick="window.location.href=\'http://undertheowl.pl/cms/view.php?header_code='. $header_code .'\'"></span>';
    echo '<span class="actions return text" onclick="window.location.href=\'http://undertheowl.pl/cms/view.php?header_code='. $header_code .'&search='. $GLOBALS['search'] .'\'">Wyczyść</span>';
    echo '<span class="actions return text" onclick="view.submit()">Przelicz</span>';
}
echo '</div></div></div><br>';

// Menu dla dokumentu
echo '<nav class="div-context-menu"><nav class="context-menu-list">Edytuj</nav><nav class="context-menu-list">Usuń</nav></nav>';
//


echo '<div class="div-table">';

//Filtr dla pojedyńczych kolumn
if(isset($GLOBALS['search']) && $GLOBALS['search']) {
    echo '<div class="div-header-row">';
    foreach ($headers as $header) {
        if(contains($header['attr'],'V')) {
            echo '<div class="div-cell border">';
            if (contains($header['attr'], 'F')) {
                echo '<div id="' . $header['code'] . '">';
                $distinctFieldNames = $process->prepareDistinctFieldNames($header['code'], $GLOBALS['search'], '');
                if(!contains('option,list',$header['type']))
                echo distinct_occurance($header['code'], $distinctFieldNames);
                else
                MultiSelectBox($header,$GLOBALS['search'],$GLOBALS['filters']);
//                echo selectbox($header_code, $header['code'], $distinctFieldNames, 'filters'); chyba stare
                echo '</div>';
            }
            echo '</div>';
        }
    }
    echo '</div>';
}

//Nagłówki
echo '<div class="div-header-row">';
foreach($headers as $header) {
    if(contains($header['attr'],'V')) {
        echo '<div class="div-cell border">';
        echo '<div class="name">' . $header['name'] . '</div>';
        if (contains($header['attr'], 'O'))
            echo order($header['code']);
        echo '</div>';
    }
}
echo '</div>';

//Dokumenty
foreach($documents as $document)
{
    echo '<div class="div-row" id='.$document['u_id']['value'].' onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?header_code='.$GLOBALS['header_code'].'&mode=show&u_code='.$document['u_code']['value'].'&id='.$document['u_id']['value'].'&u_line_number='.$document['u_line_number']['value'].'\'">';
    foreach($document as $key => $column) {
        if(contains($column['attr'],'V')) {
            echo '<div class="div-cell border">';
            echo '<div class="header-button">' . $column['value'] . '</div>';
            echo '</div>';
        }
    }
    echo '</div>';
}
echo '</div>';
echo '<input type="submit" style="visibility: hidden;" />';
echo '</form>';
//Konec strony
echo '</div></div>';


echo '<script>
$(document).ready(function(){
    $(\'[data-toggle="tooltip"]\').tooltip();   
});
</script>
<script>
    $(\'#form input\').keydown(function(e) {
        if (e.keyCode == 13) {
            $(\'#form\').submit();
        }
    });
</script>
<script src="context-menu.js"> </script>';

unset($process);
unset($config);
?>

</body>
</html>
