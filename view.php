<?php
//ini_set('display_errors', 1);


foreach ($_GET as $key => $val) $GLOBALS[$key] = $val;
foreach ($_POST as $key => $val) $GLOBALS[$key] = $val;
foreach ($_COOKIE as $key => $val) $GLOBALS[$key] = $val;

require('settings.php');
require('functions_view.php');

$sql='SELECT uh_name FROM uto_headers WHERE uh_code="'.$GLOBALS['header_code'].'"';
$db->query($sql);
if($db->next_record())
    $header=$db->f('uh_name');

if($GLOBALS['order']) {
    $order = $GLOBALS['order'];
    if ($GLOBALS['desc'])
        $order .= ' desc';
}



?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo $header; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=PT+Sans+Narrow" rel="stylesheet">
<link rel="stylesheet" href="style.css" type="text/css">
<script>
    $('#form input').keydown(function(e) {
        if (e.keyCode == 13) {
            $('#form').submit();
        }
    });
</script>
</head>
<?php
echo '<body name="'.$GLOBALS['header_code'].'">';
$cont_in = '<div class="content"><div class="container">';
$cont_out = '</div></div>';
$fields=get_fields_heading($GLOBALS['header_code']);

echo $cont_in;

//echo '<div class="div-table"><div class="div-row">';
//echo '<div class="div-cell border"><div class="header-button center" onclick="window.location.href=\'http://undertheowl.pl/cms\'">Powrót</div></div>';
//echo '<div class="div-cell border"><div class="header-button center" onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?mode=create&header_code='.$header_code.'\'">Dodaj nową nieruchomość</div></div>';
//echo '<div class="div-cell border"><div class="header-button center" onclick="window.location.href=\'http://undertheowl.pl/cms/view.php?header_code='.$header_code.'\'">Reset filtra</div></div>';
//echo '</div></div>';

echo '<br>';
echo '<form>';
send_hidden('header_code',$header_code);
$sql_filter = array();
foreach($_GET as $key => $value){
    if($key == 'header_code' || $key == 'order' || $key == 'desc' || $key == 'search' || $key == 'search_sql' || empty($value))
        continue;
    send_hidden($key,$value);
    $_filter[] = $key . '=' . $value;
    if(is_numeric($value))
        $sql_filter[] = $key . '=' . $value;
    else
        $sql_filter[] = $key . '="' . $value . '"';

}

$filter = implode(' AND ',$sql_filter);


if($GLOBALS['search']){
    $search_sql = search_sql($GLOBALS['search'],$fields);
    $_filter[] = 'search=' . $GLOBALS['search'];

}

echo '<div class="div-table">';
//Wyszukiwarka
echo '<div class="div-header-row"><div class="div-cell nopadding border search">';
echo '<span '.tooltip('Powrót').' class="glyphicon glyphicon-arrow-left actions return" onclick="window.location.href=\'http://undertheowl.pl/cms\'"></span>';
echo '<span '.tooltip('Szukaj').' class="actions return"><input name="search" class="search" value="'.$GLOBALS['search'].'"></span>';
echo '<span '.tooltip('Dodaj').' class="glyphicon glyphicon-plus actions main-actions" onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?mode=create&header_code='.$header_code.'\'"></span>';
if($GLOBALS['search']){
    echo '<span '.tooltip('Zamknij').' class="glyphicon glyphicon-remove actions return" onclick="window.location.href=\'http://undertheowl.pl/cms/view.php?header_code='. $header_code .'\'"></span>';
    echo '<span class="actions return text" onclick="window.location.href=\'http://undertheowl.pl/cms/view.php?header_code='. $header_code .'&search='. $GLOBALS['search'] .'\'">Wyczyść</span>';
}
echo '</div></div></div><br>';

// Menu dla dokumentu
echo '<nav class="div-context-menu"><nav class="context-menu-list">Edytuj</nav><nav class="context-menu-list">Usuń</nav></nav>';
//


echo '<div class="div-table">';
//if($GLOBALS['search']){
//    send_hidden('search',$GLOBALS['search']);
//}
//Filtr dla pojedyńczych kolumn
if($GLOBALS['search']) {
    echo '<div class="div-header-row">';
    foreach ($fields as $field) {
        echo '<div class="div-cell '.($field['code'] != 'u_id' ? 'border' : '').'">';
        if ($field['code'] != 'u_id') {
            echo '<div id="' . $field['code'] . '">';
//            echo '<input style="width:46px; display:table-cell; " name="' . $field['code'] . '" value="">';
            echo distinct_occurance($field['code'], $header_code, $search_sql, $filter);
            echo '</div>';
        }
        echo '</div>';
    }
    echo '</div>';
}
//Koniec filtrów

//Nagłówki
echo '<div class="div-header-row">';
foreach($fields as $field) {
    echo '<div class="div-cell border">';
//    echo '<div class="name" onclick="document.getElementById(\''. $field['code'] .'\').style.display=\'-webkit-inline-box\'">' . $field['name'] . '</div>';
    echo '<div class="name" onclick="javascript: {x=document.getElementById(\''. $field['code'] .'\').style.display; if (x==\'none\') document.getElementById(\''. $field['code'] .'\').style.display=\'-webkit-inline-box\'; else document.getElementById(\''. $field['code'] .'\').style.display=\'none\'; }">' . $field['name'] . '</div>';
    if($field['name'] != 'Numer' && $field['name'] != "Lp.")
        echo filter($header_code,$field['code'],$_filter) ;
    echo '</div>';
}
echo '</div>';
//echo '<div class="div-row">';
//foreach($fields as $field) {
//    echo '<div class="div-cell border">';
//    echo '<input style="width:46px; display:none; " name="' . $field['code'] . '" value="">';
//    echo '</div>';
//}
//echo '</div>';
$rows = get_fields($GLOBALS['header_code'],$fields,'',$order,$filter,$search_sql);

$i=1;
foreach($rows as $key => $row)
{
    echo '<div class="div-row" id='.$row['u_id'].' onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?header_code='.$GLOBALS['header_code'].'&mode=show&u_code='.$row['u_code'].'&id='.$row['u_id'].'&u_line_number='.$row['u_line_number'].'\'">';

    foreach($row as $key => $value) {
        echo '<div class="div-cell border">';
        if ($key=="u_id")
            echo '<div class="header-button">' . $value . '</div>';
        else
            echo '<div class="header-button">' . $value . '</div>';
        echo '</div>';
    }
    echo '</div>';
    $i++;
}
echo '</div>';
echo '<input type="submit" style="visibility: hidden;" />';
echo '</form>';
echo $cont_out;


echo '<script>
$(document).ready(function(){
    $(\'[data-toggle="tooltip"]\').tooltip();   
});
</script>
<script src="context-menu.js"> </script>';
?>

</body>
</html>
