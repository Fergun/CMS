<?php
/**
 * Created by PhpStorm.
 * User: MrCzumix
 * Date: 2018-03-29
 * Time: 20:25
 */

function MultiSelectBox($type,$field,$search,$filter,$lines_row_number=-1,$lines_code=0){
    global $db, $header_code;
    $only_values = 0;

    switch($field['type']){

        case 'option':
            $sql = 'SELECT uo_value as value, uo_name as descr FROM uto_options WHERE uo_class = "'.$field['using_table'].'"';
            break;
        case 'list':
            $sql = 'SELECT DISTINCT ';
            $select_parts = explode(" | ",$field['column']);
            for($i=0;$i<count($select_parts);$i++){
                $select_statement[]= $select_parts[$i] . ' as ' . ($i ? 'descr' : 'value');
            }
            $sql .= implode(', ',$select_statement);
            $sql .= ' FROM '.$field['using_table'].' WHERE u_code = "'.$header_code.'"';
            if(count($select_parts) == 1){
                $only_values = 1;
            }
            break;
        default:
            $sql = 'SELECT DISTINCT ' . $field['code'] . ' as value FROM uto_'.$header_code;
            $only_values = 1;
            break;
    }
    $db->query($sql);
    while($db->next_record()){
        $value = $db->f('value');
        $descr = $db->f('descr');

        $values[] = array(
            'value' => $value,
            'descr' => ($only_values ? $value : $descr),
        );
    }


    if(contains($field['attr'],'R') || $type == 'filters'){
      $refresh = 'class="refresh"';
    }
    if(contains($field['attr'],'M') || $type == 'filters'){
        $multiple = 'id="multiselectbox" multiple="multiple"';
    }
    switch($type){
        case 'filters':
            $select_name = $type.'[' . $field['code'] . '][]';
            $selected_values = $GLOBALS['filters'][$field['code']];
            $sql = '';
            foreach ($GLOBALS['filters'] as $column_name => $column_values){
                $sql .= ' AND CONCAT(", ",' . $column_name . ',",") REGEXP ", (';
                $in = array();
                foreach ($column_values as  $column_value) {
                    $in[] = $column_value;
                }
                $sql .= implode('|',$in);
                $sql .= '),"';
            }
            break;
        case 'header':
            $select_name = $header_code.'[' . $field['code'] . ']' .($multiple ? '[]' : '');
            $selected_values = explode(', ',$GLOBALS['row'][$field['code']]);
            break;
        case 'lines':
            $select_name = $lines_code.'['.$lines_row_number.'][' . $field['code'] . '_' . $lines_row_number .']' .($multiple ? '[]' : '');
            $selected_values = explode(', ',$GLOBALS['lines_rows'][$lines_code][$lines_row_number][$field['code']]);
            break;
    }

    echo '<select name="'.$select_name.'" '.$refresh.' '.$multiple.'>';
    if(!$multiple){
        echo '<option value="">(brak)</option>';
    }
    foreach($values as $value) {
        $selected = '';
        if(in_array($value['value'], $selected_values) && !$GLOBALS['clear']){
            $selected = 'selected';
        }
        echo '<option value="'.$value['value'].'" '.$selected.'>'.$value['descr'].($type == 'filters' ? ' ('.uto_query('SELECT count(*) FROM uto_'.$header_code.' WHERE CONCAT(", ",'.$field['code']. ',",") LIKE "%, '.$value['value'].',%"' . $sql).')' : '').'</option>';
    }
    echo '</select>';
}
