<?php

function create_table($id,$table,$post){

    $sql = 'CREATE TABLE IF NOT EXISTS `uto_'. $table .'` (
    `u_code` VARCHAR(50) NULL,
    `u_id` int(6) NOT NULL,
  `u_line_number` int(6) NOT NULL,
  `u_status` int(6) unsigned NOT NULL,
  CONSTRAINT PK_'. $table .' PRIMARY KEY (u_code,u_id,u_line_number)
) ENGINE=InnoDB DEFAULT CHARSET=latin2;';
    uto_query($sql);
}

function delete_table($id,$table,$post){
    //Usuwam Tabele
    $sql = 'DROP TABLE `uto_'. $table .'`;';
    uto_query($sql);
}

function edit_table($id,$table,$post)
{
    $table = 'uto_'. $table;
    foreach ($post['fields'] as $key => $field) {
        $is = uto_query('SELECT count(*) FROM uto_fields WHERE u_id = '. $id .' AND uf_code = "'. $field['uf_code_'. $key] .'"');
        if(!$is) {
            $sql = 'ALTER TABLE ' . $table . ' ADD ' . $field['uf_code_' . $key] . ' ' . column_type($field['uf_type_' . $key]);
            uto_query($sql);
        }
    }
}

function column_type($field_type){
    switch($field_type){
        case 'line':
        case 'list':
        case 'option':
            $db_type = 'VARCHAR(MAX)';
            break;
        case 'int':
        case 'numeric':
            $db_type = 'INT(9)';
            break;
        case 'date':
            $db_type = 'DATETIME(6)';
    }
    return $db_type;
}


function get_fields_heading($header)
{
    global $db;

    $sql = 'SELECT uf_name, uf_code FROM uto_fields f, uto_headers h WHERE f.u_id=h.u_id AND uh_code="' . $header . '" order by f.u_line_number';

    $db->query($sql);

    $fields = array();
    $i=0;
//    $fields[$i]['name'] = 'Lp.';
//    $fields[$i]['code'] = 'u_id';
//    $i++;
    while($db->next_record()) {
        $fields[$i]['name'] = $db->f('uf_name');
        $fields[$i]['code'] = $db->f('uf_code');
        $i++;
    }
    if($i==1)
        return;
    else
        return $fields;
}

function get_fields($header,$fields_code,$u_code,$order=0, $filter=0, $search=0)
{
    global $db;

    $sql = 'SELECT * FROM uto_'.$header;
    if($u_code)
        $sql .= ' WHERE u_code ="'. $u_code . '"';
    elseif($search && $filter)
        $sql .= $search .' AND '. $filter ;
    elseif($search)
        $sql .= $search;
    elseif($filter)
        $sql .= ' WHERE ' . $filter;

    if($order)
        $sql .= ' ORDER BY ' . $order;

    $db->query($sql);

    $rows=array();
    $i=1;
    while($db->next_record()) {
        foreach($fields_code as $field){
            $rows[$i][$field['code']] = $db->f($field['code']);
        }
        $i++;
    }
    return $rows;
}

function get_doc_data($process_code,$proces_fields,$u_code,$u_id,$u_line_number)
{
    global $db;
    if(!$u_code){
        $u_line_number = 1;
    }
    $sql = 'SELECT * FROM uto_'. $process_code .' WHERE u_code = "'. $u_code .'" AND u_id = '. $u_id .' AND u_line_number = '. $u_line_number .';';
    $db->query($sql);
    if($db->next_record()) {
        foreach($proces_fields as $field){
            $row[$field['code']] = $db->f($field['code']);
        }

    }
    return $row;
}

function get_fields_lines_heading($header)
{
    global $db;
    $sql = 'SELECT uc_minor FROM uto_connections WHERE uc_major = "'. $header .'";';
    $db->query($sql);
    while($db->next_record()) {
        $connect_tables[] = $db->f(0);
    }
    if($connect_tables){
        $fields = array();
        foreach($connect_tables as $connect_table) {
            $sql = 'SELECT uf_code, uf_name FROM uto_fields f,uto_headers h WHERE f.u_id = h.u_id AND h.uh_code = "' . $connect_table . '";';
            $db->query($sql);
            $i = 0;
            while ($db->next_record()) {
                $fields[$connect_table][$i]['code'] = $db->f('uf_code');
                $fields[$connect_table][$i]['name'] = $db->f('uf_name');
                $i++;
            }
        }
        return $fields;
    }
}

function get_fields_lines($header_code,$lines_header_code,$fields_code,$id)
{
    global $db;
    $sql = 'SELECT * FROM uto_' . $lines_header_code . ' WHERE u_code = "'. $header_code .'" AND u_id=' . $id ;
    $db->query($sql);

    $rows = array();
    $i = 0;
    while ($db->next_record()) {
        foreach ($fields_code as $field) {
            $rows[$i][$field['code']] = $db->f($field['code']);
        }
        $i++;
    }

    return $rows;
}

function if_edit($table,$nr,$mode,$code,$name)
{
    $_name=$name;
    if(!is_numeric($name))
        $name='"' . $name . '"';
    if($nr != -1)
        $string = $nr .'][' ;

    if ($mode == 'edit' || $mode == 'create') {
        echo '<input name="' . $table .'['. $string . $code . ']" value=' . $name . '>';
    } elseif($mode == 'hidden') {
        echo '<input type="hidden" name="' . $table .'['. $string . $code . ']" value=' . $name . '>';
    } else {
        echo $_name . '<input type="hidden" name="' . $table .'['. $string . $code . ']" value=' . $name . '>';
    }
}

function uto_query($sql)
{
    global $db;

    $db->query($sql);
    if (!$db->Errno && contains($sql, 'select')) {
        $db->next_record();

        return $db->f(0);
    }
}

function contains($string, $what)
{
    if ($string != "" && !is_array($string) && $what != "" && stripos($string, $what) !== false)
        return 1;
    else
        return 0;
}

function send_hidden($name, $value = '__empty__')
{
  if ($value === '__empty__')
    $value = $GLOBALS[$name];

  echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
}

function send_hidden_array($row_name, $cell_name,$value = '__empty__')
{
  if ($value === '__empty__')
    $value = $GLOBALS[$row_name][$cell_name];

  echo '<input type="hidden" name="'.$row_name .'['. $cell_name.']" value="'.$value.'">';
}

function edit($header_code, $headers, $post)
{
    global $db;
    $sql = 'UPDATE uto_' . $header_code . ' SET ';
    foreach($headers as $key => $column_name){
        if($column_name['code']=='u_id')
        {
            $id=$post[$column_name['code']];
            continue;
        }
        if($column_name['code']=='u_code')
        {
            $code=$post[$column_name['code']];
            continue;
        }
        if($column_name['code']=='u_line_number')
        {
            $line_number=$post[$column_name['code']];
            continue;
        }
        $value = $post[$header_code][$column_name['code']];
        $value = str_replace('"', '\"', $value);
        $value = str_replace("'", "\'", $value);
        if(is_array($value)){
            $value = implode(', ',$value);
        }
        if(is_numeric($post[$column_name['code']]))
            $sql .= $column_name['code'] . '=' .  $value ;
        else
            $sql .= $column_name['code'] . '="' .  $value . '"' ;

        if(($key + 1) == count($headers))
            $sql .= ' ';
        else
            $sql .= ', ';
    }

    $sql .= 'WHERE u_id='. $id .' AND u_code = "'.$code.'" AND u_line_number = '.$line_number.';';
//    $db->query($sql);
    $sqls[] = $sql;
    foreach ($post['line_codes'] as $line_header) {
        $sqls[] = 'DELETE FROM uto_'. $line_header .' WHERE u_code = "'. $header_code .'" AND u_id = '. $id .';';
        $sql = 'DELETE FROM uto_'. $line_header .' WHERE u_code = "'. $header_code .'" AND u_id = '. $id .';';
//        $db->query($sql);
        foreach( $post[$line_header] as $row){
            $sql = 'INSERT INTO uto_'. $line_header;
            $names = array();
            $values = array();
            foreach($row as $cell_name => $cell_value){
//                $name = substr($cell_name, 0, strlen($cell_name)-2);
                $name = preg_replace('/_\d+$/','',$cell_name);
                if($name == 'delete' || !$name){
                    continue;
                }
                $names[] = $name;
                if($name == 'u_id'){
                    $cell_value = $id;
                }
                if(is_array($cell_value)){
                    $cell_value = implode(', ',$cell_value);
                }
                if(!is_numeric($cell_value)){
                    $cell_value = '"'. $cell_value . '"';
                }
                $values[] = $cell_value;
            }
            $sql .= ' (' . implode(',', $names) . ') VALUES (' . implode(',', $values) . ');';
            if(count($names) && count($values)) {
                $sqls[] = $sql;
            }
//            $db->query($sql);
        }
    }

    foreach ($sqls as $sql) {
        uto_query($sql);
    }
}

function create($header_code, $headers, $post)
{
    global $db;
    $sql = 'SELECT MAX(u_id) FROM uto_' . $header_code . ' ';
    $db->query($sql);
    $db->next_record($sql);
    $id = $db->f(0) + 1;
    $cell_name = '';
    $cell_value = '';

    foreach($headers as $key => $column_name){
        if($column_name['code']=='u_id')
        {
            $cell_name .= $column_name['code'] . ',';
            $cell_value .= $id . ',';
            continue;
        }
        $value = $post[$header_code][$column_name['code']];
        if(is_array($value)){
            $value = implode(', ',$value);
        }
        if(is_numeric($value)) {
            $cell_value .= $value;
        }
        else{
            $cell_value .= '"' . $value . '"';
        }

        $cell_name .= $column_name['code'];

        if(($key + 1) != count($headers)) {
            $cell_name .= ',';
            $cell_value .= ',';
        }
    }

    $sql='INSERT INTO uto_' . $header_code . '('. $cell_name .') values ('. $cell_value .')';
    $db->query($sql);
    if($header_code == 'headers'){
        $front = 'INSERT INTO uto_fields (u_code,u_id,u_line_number,uf_code,uf_name,uf_type) VALUES ("headers",' . $id;
        $sqls[] = $front . ',1,"u_code","Kod procesu","line")';
        $sqls[] = $front . ',2,"u_id","Lp","int")';
        $sqls[] = $front . ',3,"u_line_number","Numer","int")';
        $sqls[] = $front . ',4,"u_status","Status","list")';
        foreach($sqls as $sql){
            uto_query($sql);
        }
    }
}


function delete($header_code, $headers, $post)
{
    $id = $post[$header_code]['u_id'];
    $sqls[] = 'DELETE FROM uto_' . $header_code . ' WHERE u_id = '. $id .';';

    foreach ($post['line_codes'] as $line_header) {
        $sqls[] = 'DELETE FROM uto_'. $line_header .' WHERE u_id = '. $id .';';
    }

    foreach ($sqls as $sql) {
        uto_query($sql);
    }
}

function order($field_code)
{

    $link = 'onclick="view.order.value=\''. $field_code .'\';view.desc.value=0;view.submit();"';
    $link_desc = 'onclick="view.order.value=\''. $field_code .'\';view.desc.value=1;view.submit();"';

//    $numeric = rand(0,1);
//    $glyph = 'glyphicon glyphicon-sort-by-'. ($numeric ? 'order' : 'alphabet') . ($GLOBALS['desc'] ? '-alt' : '');
//    $link_auto = 'onclick="window.location.href=\'http://undertheowl.pl/cms/view.php?header_code='.$header_code.'&order='.$field_code . ($GLOBALS['desc'] ? '&desc=1' : '') .'\'"';

    $filter ='<div class="filter">';
    $filter .= '<div class="arrow-up" '.$link.'"></div>';
    $filter .= '<div class="arrow-down" '.$link_desc.'"></div>';
    $filter .= '</div>';
//    $filter .= '<span class="filter '. $glyph .'"></span>';
    echo $filter;
}

function distinct_occurance($field_name, $distincts){

//    $string = '<select name="filters[' . $field_name . ']" onchange="this.form.submit()" multiple="multiple">';
    $string = '<select name="filters[' . $field_name . '][]" multiple="multiple">';
//    $string .= '<option value="">(wszystkie)</option>';
    foreach($distincts as $distinct){
        if($distinct == null){
            continue;
        }
        if(in_array($distinct, $GLOBALS['filters'][$field_name]) && !$GLOBALS['clear'])
            $string .= '<option value="'. $distinct .'" selected>'. $distinct .'</option>';
        else
            $string .= '<option value="'. $distinct .'">'. $distinct .'</option>';
    }
    $string .= '</select>';

    return $string;
}

function tooltip($text){
    $string = 'data-toggle="tooltip" data-placement="bottom" data-trigger="hover" title="'. $text .'"';
    return $string;
}