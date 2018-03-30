<?php
/**
 * Created by PhpStorm.
 * User: MrCzumix
 * Date: 2018-03-29
 * Time: 20:25
 */

function MultiSelectBox($field,$search,$filter){
    global $db, $header_code;

    switch($field['type']){

        case 'option':
            $db_value = 'uo_value';
            $db_name = 'uo_name';
            $sql = 'SELECT '.$db_value.','.$db_name.' FROM uto_options WHERE uo_class = "'.$field['using_table'].'"';
            break;
        case 'list':
            $db_value = $field['column'];
            $db_name = $field['column'];
            if(contains($field['column']," | ")){
                $dupa = explode(" | ",$field['column']);
                $db_value = $dupa[0];
                $db_name = $dupa[1];
            }

            $sql = 'SELECT DISTINCT '.$db_value.','.$db_name.' FROM '.$field['using_table'].' WHERE u_code = "'.$header_code.'"';
            break;
        default:
            break;
    }
    $db->query($sql);
    while($db->next_record()){
        $values[] = array(
            'value' => $db->f($db_value),
            'name'  => $db->f($db_name)
        );
    }

    if(contains($field['attr'],'R')){
      $refresh = 'class="refresh"';
    }

    echo '<select name="filters[' . $field['code'] . '][]" '.$refresh.' id="multiselectbox" multiple="multiple">';
    foreach($values as $value){
        $selected = '';
        if(in_array($value['value'], $GLOBALS['filters'][$field['code']]) && !$GLOBALS['clear'])
            $selected = 'selected';

        echo '<option value="'.$value['value'].'" '.$selected.'>'.$value['name'].'</option>';
    }
    echo '</select>';
}
