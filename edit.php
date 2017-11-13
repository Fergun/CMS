<?php
require('support/init.php');

require('system/Config.php');
require('system/Process.php');
$config = new Config();
$indexes = $config->getData()['indexes'];
$process = new Process($config->getData(),$GLOBALS['header_code']);
//Załadowanie nagłówków
$headers = $process->getProcessHeaders();

$mode = $GLOBALS['mode'];
$header_code = $GLOBALS['header_code'];
$code = $GLOBALS['u_code'];
$id = $GLOBALS['id'];
$line_number = $GLOBALS['u_line_number'];

if(!contains($mode,'create')) {
    $row = get_doc_data($header_code, $headers, $code, $id, $line_number);
}
if($mode=='to_create'){
    if($header_code == 'headers'){
        create_table($GLOBALS['_POST']['headers']['uh_code']);
    }
    insert($header_code,$headers,$GLOBALS['_POST']);
    header('Location: http://undertheowl.pl/cms/view.php?header_code='.$header_code);
    exit;
}
if($mode=='to_edit') {
    if($header_code == 'headers') {
        modify_table($id,$GLOBALS['_POST']['headers']['uh_code'], $GLOBALS['_POST']);
    }
    update($header_code,$headers,$GLOBALS['_POST']);
    header('Location: http://undertheowl.pl/cms/edit.php?mode=show&id='.$id.'&header_code='.$header_code);
    exit;
}
if($mode=='to_delete'){
    if($header_code == 'headers'){
        delete_table($GLOBALS['_POST']['headers']['uh_code']);
    }
    delete($header_code,$headers,$GLOBALS['_POST']);
    header('Location: http://undertheowl.pl/cms/view.php?header_code='.$header_code);
    exit;
}

$title = $process->getProcessName();
require('support/style.php');

echo '<body name="'.$GLOBALS['header_code'].'">';
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
        echo '<span class="header text">'. $title .'</span>';
        if($mode == 'show'){
            echo '<span '.tooltip('Usuń').' class="glyphicon glyphicon-trash actions main-actions" onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?mode=delete&header_code='.$header_code.'&u_code='.$code.'&id='.$id.'&u_line_number='.$line_number.'\'"></span>';
            echo '<span '.tooltip('Edytuj').' class="glyphicon glyphicon-pencil actions main-actions" onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?mode=edit&header_code='.$header_code.'&u_code='.$code.'&id='.$id.'&u_line_number='.$line_number.'\'"></span>';
        }
    echo '</div></div></div><br>';

    echo '<div class="div-table">';
            foreach($headers as $key => $header){
                $hidden = '';
                $tmp_mode = $mode;
                $value = $row[$header['code']];
                if($header['code'] == 'u_code' && $header_code != 'fields'){
                    $tmp_mode = 'hidden';
                    $hidden = 'hidden';
                    $value = ($row[$header['code']] ? $row[$header['code']] : '');
                }
                if($header['code'] == 'u_id'){
                    $tmp_mode = 'show';
                }
                if($header['code'] == 'u_line_number' || ($header['code'] == 'u_id' && $mode == 'create')){
                    $value = 1;
                    $tmp_mode = 'hidden';
                    $hidden = 'hidden';
                }
                echo '<div class="div-row '. $hidden .'">';

                echo '<div class="div-cell border" style="width:1%">';
                echo '<div class="header-button" name="' . $header['code'] . '">' . $header['name'] . '</div>';
                echo '</div>';

                echo '<div class="div-cell border" style="width:99%">';
                echo '<div class="header-button" name="' . $header['code'] . '">'.if_edit($header_code,-1,$tmp_mode,$header['code'],$value).'</div>';
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
        echo '<div class="div-row" '. ($mode == 'show' ? 'onclick="window.location.href=\'http://undertheowl.pl/cms/edit.php?header_code='.$lines_header.'&mode=show&id='.$id.'&u_line_number='.($i+1).'&u_code='.$header_code.'\'"' : 'onclick="document.getElementsByName(\'' . $lines_header . '[' . $nr . '][delete_' . $i . ']\')[0].click();"') .'>';
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
        echo '<span ' . tooltip('Dodaj') . ' class="actions border return" onclick="edit.mode.value=\'' . $mode . '\';edit.ile_plus_'.$nr.'.value=increm(edit.ile_plus_'.$nr.'.value,1);edit.submit();">Dodaj</span>';
        echo '<span ' . tooltip('Usuń') . ' class="actions border main-actions" onclick="edit.mode.value=\'' . $mode . '\';edit.ile_plus_'.$nr.'.value=increm(edit.ile_plus_'.$nr.'.value,0);edit.submit();">Usuń</span>';
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
$(document).ready(function () {
    $(this).keydown(function (e) {
    
      if (e.ctrlKey && e.keyCode == 13) {
        edit.mode.value="to_' . $mode . '";
        edit.submit();
      }
    });
}); 
</script>';
?>

</body>
</html>
