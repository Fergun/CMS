<?php
foreach ($_GET as $key => $val) $GLOBALS[$key] = $val;
foreach ($_POST as $key => $val) $GLOBALS[$key] = $val;
foreach ($_COOKIE as $key => $val) $GLOBALS[$key] = $val;

require('settings.php');
require('functions_view.php');

$sql='SELECT uh_name FROM uto_headers WHERE uh_code="'.$GLOBALS['header_code'].'"';
$db->query($sql);
if($db->next_record())
    $header=$db->f('uh_name');

$mode=$GLOBALS['mode'];
$header_code=$GLOBALS['header_code'];
$id=$GLOBALS['id'];
$code=$GLOBALS['u_code'];


$headings = get_fields_heading($header_code);
$rows = get_fields($header_code, $headings, $code);
$row = $rows[$id];

if($mode=='to_create'){
    if($header_code == 'headers'){
        create_table($GLOBALS['_POST']['headers']['uh_code']);
    }
    insert($header_code,$headings,$GLOBALS['_POST']);
    header('Location: http://undertheowl.pl/cms/view.php?header_code='.$header_code);
    exit;
}
if($mode=='to_edit') {
    if($header_code == 'headers') {
        modify_table($id,$GLOBALS['_POST']['headers']['uh_code'], $GLOBALS['_POST']);
    }
    update($header_code,$headings,$GLOBALS['_POST']);
    header('Location: http://undertheowl.pl/cms/edit.php?mode=show&id='.$id.'&header_code='.$header_code);
    exit;
}
if($mode=='to_delete'){
    if($header_code == 'headers'){
        delete_table($GLOBALS['_POST']['headers']['uh_code']);
    }
    delete($header_code,$headings,$GLOBALS['_POST']);
    header('Location: http://undertheowl.pl/cms/view.php?header_code='.$header_code);
    exit;
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
</head>
<body>
<?php
$cont_in = '<div class="content"><div class="container">';
$cont_out = '</div></div>';

//Początek dokumentu
echo $cont_in;
echo '<br>';

echo '<form action="edit.php" name="edit" id="edit" method="post">';
    $old_mode=$mode;
    send_hidden('mode',$mode);
    send_hidden('old_mode',$old_mode);
    send_hidden('header_code',$header_code);
    send_hidden('id',$id);
    send_hidden('u_id',$id);

    echo '<div class="div-table"><div class="div-header-row"><div class="div-cell nopadding border">';
        echo '<span '.tooltip('Powrót').' class="glyphicon glyphicon-arrow-left actions return" onclick="window.location.href=\'http://undertheowl.pl/cms/view.php?header_code='.$header_code.'\'"></span>';
        echo '<span class="header text">'. uto_query('SELECT uh_doc_name FROM uto_headers WHERE uh_code ="'. $header_code .'"').'</span>';
        if($mode == 'show'){
            echo '<span '.tooltip('Usuń').' class="glyphicon glyphicon-trash actions main-actions" onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?mode=delete&id='.$id.'&header_code='.$header_code.'\'"></span>';
            echo '<span '.tooltip('Edytuj').' class="glyphicon glyphicon-pencil actions main-actions" onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?mode=edit&id='.$id.'&header_code='.$header_code.'\'"></span>';
        }
    echo '</div></div></div><br>';

    echo '<div class="div-table">';
            foreach($headings as $key => $heading){
                $hidden = '';
                $tmp_mode = $mode;
                $value = $row[$heading['code']];
                if($heading['code'] == 'u_code'){
                    $tmp_mode = 'hidden';
                    $hidden = 'hidden';
                    $value = ($row[$heading['code']] ? $row[$heading['code']] : '');
                }
                if($heading['code'] == 'u_id'){
                    $tmp_mode = 'show';
                }
                if($heading['code'] == 'u_line_number' || ($heading['code'] == 'u_id' && $mode == 'create')){
                    $value = 1;
                    $tmp_mode = 'hidden';
                    $hidden = 'hidden';
                }
                echo '<div class="div-row '. $hidden .'">';

                echo '<div class="div-cell border" style="width:1%">';
                echo '<div class="header-button" name="' . $heading['code'] . '">' . $heading['name'] . '</div>';
                echo '</div>';

                echo '<div class="div-cell border" style="width:99%">';
                echo '<div class="header-button" name="' . $heading['code'] . '">'.if_edit($header_code,-1,$tmp_mode,$heading['code'],$value).'</div>';
                echo '</div>';

                echo '</div>';
            }
    echo '</div>';

//Linie
$lines_headings = get_fields_lines_heading($header_code);
$nr = 0;
foreach($lines_headings as $lines_header => $line_headings) {
    if ($mode != 'create') {

        $lines_rows = get_fields_lines($header_code,$lines_header, $line_headings, $id);
        if ($lines_rows) {
            $sql = 'SELECT MAX(u_line_number) FROM uto_' . $lines_header . ' WHERE u_id=' . $id;
            $db->query($sql);
            if ($db->next_record())
                ${'ile'.$nr} = $db->f(0);
        }
    }
    send_hidden_array('line_codes',$nr,$lines_header );
    if ($mode == 'edit' || (${'ile'.$nr} && $mode == 'show')) {
        echo '<br>';
        echo '<div class="div-table"><div class="div-header-row"><div class="div-cell nopadding center border">';
        echo '<span class="header text">'. uto_query('SELECT uh_name FROM uto_headers WHERE uh_code ="'. $lines_header .'"').'</span>';
        echo '</div></div></div>';
    }

    echo '<div class="div-table">';
    if($mode == 'edit' || (${'ile'.$nr} && $mode == 'show')){
        echo '<div class="div-header-row">';
        foreach($line_headings as $column => $line_heading) {
            $hidden = '';
            if($line_heading['code'] == 'u_id' || $line_heading['code'] == 'u_code'){
                $hidden = 'hidden';
            }
            echo '<div class="div-cell border '.$hidden.'">';
            echo '<div class="name">'. $line_heading['name'] .'</div>';
            echo '</div>';
        }
        if($mode == 'edit') {
            echo '<div class="div-cell border">';
            echo '<div class="name">Usuń</div>';
            echo '</div>';
        }
        echo '</div>';
    }
    if (isset($GLOBALS['ile_plus_'.$nr])) 
        ${'ile'.$nr} = $GLOBALS['ile_plus_'.$nr];
    for ($i = 0; $i < ${'ile'.$nr}; $i++) {
        echo '<div class="div-row" '. ($mode == 'show' ? 'onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?header_code='.$lines_header.'&mode=show&id='.$id.'&u_code='.$header_code.'\'"' : 'onclick="console.log(document.getElementsByName(\'' . $lines_header . '[' . $nr . '][delete_' . $i . ']\')[0].click());"') .'>';
        foreach($line_headings as $column => $line_heading) {
            $tmp_mode = $mode;
            $value = ($GLOBALS[$lines_header][$i][$line_heading['code'] . '_' . $i] ? $GLOBALS[$lines_header][$i][$line_heading['code'] . '_' . $i] : $lines_rows[$i][$line_heading['code']]);
            $hidden = '';
            if($line_heading['code'] == 'u_code'){
                $hidden = 'hidden';
                $tmp_mode = 'hidden';
                $value = $header_code;
            }
            if($line_heading['code'] == 'u_id'){
                $hidden = 'hidden';
                $tmp_mode = 'hidden';
                $value = $id;
            }
            if($line_heading['code'] == 'u_line_number'){
                $tmp_mode = 'show';
                $value = $i+1;
            }
            echo '<div class="div-cell border '.$hidden.'" >';
            echo if_edit($lines_header, $i, $tmp_mode, $line_heading['code'] . '_' . $i, $value);
            echo '</div>';
        }
        if($mode == 'edit') {
            echo '<div class="div-cell border" onclick="">';
            echo '<div class="name"><input type="checkbox" name="' . $lines_header . '[' . $nr . '][delete_' . $i . ']" value=""></div>';
            echo '</div>';
        }
        echo '</div>';
    }
    echo '</div>';

//Obsługa linii
    if ($mode == 'edit') {
        send_hidden('ile_plus_'.$nr,${'ile'.$nr});
        echo '<div class="div-table"><div class="div-header-row"><div class="div-cell nopadding">';
//        echo '<span ' . tooltip('Dodaj') . ' class="actions border return" onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?mode=' . $mode . '&id=' . $id . '&header_code=' . $header_code . '&ile_plus=' . ($ile + 1) . '\'">Dodaj</span>';
        echo '<span ' . tooltip('Dodaj') . ' class="actions border return" onclick="edit.mode.value=\'' . $mode . '\';console.log(edit.ile_plus_'.$nr.'.value);edit.ile_plus_'.$nr.'.value=increm(edit.ile_plus_'.$nr.'.value,1);edit.submit();">Dodaj</span>';
//        echo '<span ' . tooltip('Usuń') . ' class="actions border main-actions" onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?mode=' . $mode . '&id=' . $id . '&header_code=' . $header_code . '&ile_plus_'.$nr.'=' . ($ile - 1) . '\'">Usuń</span>';
        echo '<span ' . tooltip('Usuń') . ' class="actions border main-actions" onclick="edit.mode.value=\'' . $mode . '\';console.log(edit.ile_plus_'.$nr.'.value);edit.ile_plus_'.$nr.'.value=increm(edit.ile_plus_'.$nr.'.value,0);edit.submit();">Usuń</span>';
        echo '</div></div></div>';
    }
    $nr++;
}
 echo '</form>';



//Potwierdź 'Usuń' lub 'Zapisz'

if($mode != 'show'){
    echo '<br>';
    echo '<div class="div-table"><div class="div-header-row"><div class="div-cell nopadding">';
    $help = 'Zapisz';
    if($mode == 'delete'){
        $help = 'Usuń';
    }
    echo '<span '.tooltip($help).' class="actions main-actions border" onclick="edit.mode.value=\'to_' . $mode . '\';edit.submit();">'.$help.'</span>';
    echo '</div></div></div>';
}

//Koniec dokumentu
echo $cont_out;

echo '<script>
    $(\'#form input\').keydown(function(e) {
        if (e.keyCode == 13) {
            $(\'#form\').submit();
        }
    });
</script>
<script>
$(document).ready(function(){
    $(\'[data-toggle="tooltip"]\').tooltip();   
});
function increm(value,sign){
    if(sign)
        value++;
    else 
        value--;
    return value;
}
</script>';
?>

</body>
</html>
