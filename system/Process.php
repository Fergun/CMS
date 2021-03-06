<?php

class Process
{
    private $doc_code;
    private $config;
    private $headers;
    private $lines_headers;


    public function __construct($config,$doc_code)
    {
        $this->doc_code = $doc_code;
        $this->config = $config;
    }

    public function getProcessName()
    {
        global $db;

        $process_code       = $this->doc_code;

        $processTable       = $this->config['headers']['table'];
        $processName        = $this->config['headers']['name'];
        $processCode        = $this->config['headers']['code'];

        $sql = 'SELECT '. $processName .' FROM '. $processTable .' WHERE '. $processCode .' = "'. $process_code .'";';
        $db->query($sql);
        $name = '(brak)';
        if($db->next_record()){
            $name = $db->f($processName);
        }
        return $name;
    }

    public function getProcessStatus()
    {
        global $db;

        $process_code       = $this->doc_code;

        $processTable       = $this->config['headers']['table'];
        $processCode        = $this->config['headers']['code'];

        $statusTable        = $this->config['statuses']['table'];
        $statusValue        = $this->config['statuses']['value'];
        $statusName         = $this->config['statuses']['name'];

        $sql = 'SELECT u_status FROM '. $processTable . ' WHERE '. $processCode .' = "'. $process_code .'"' ;
        $db->query($sql);
        if($db->next_record()){
            $status_value = $db->f('u_status');
            $sql = 'SELECT '.$statusValue .', '. $statusName .' FROM '. $statusTable .' WHERE u_code = "headers" AND '. $statusValue .' = '. $status_value;
            $db->query($sql);
            if($db->next_record()){
                $status = array(
                    'value' => $db->f($statusValue),
                    'name' => $db->f($statusName)
                );
            }
        }
        return $status;

    }

    public function getProcessActions($processStatusValue)
    {
        global $db;

        $statusTable        = $this->config['statuses']['table'];
        $statusValue        = $this->config['statuses']['value'];
        $statusNextValue    = $this->config['statuses']['next_value'];
        $statusAction       = $this->config['statuses']['action'];

        $sql = 'SELECT '. $statusNextValue .', '. $statusAction .' FROM '. $statusTable .' WHERE u_code = "headers" AND '. $statusValue .' = ' . $processStatusValue .';';
        $db->query($sql);
        while($db->next_record()){
            $actions[] = array(
                'next_value' => $db->f($statusNextValue),
                'name' => $db->f($statusAction)
            );
        }
        return $actions;

    }

    public function getProcessHeaders($process_code = 0)
    {
        global $db;

        if(!$process_code){
            $process_code       = $this->doc_code;
        }

        $id                 = $this->config['indexes']['id'];

        $processTable       = $this->config['headers']['table'];
        $processCode        = $this->config['headers']['code'];

        $fieldsTable        = $this->config['fields']['table'];
        $fieldsName         = $this->config['fields']['name'];
        $fieldsCode         = $this->config['fields']['code'];
        $fieldsType         = $this->config['fields']['type'];
        $fieldsAttribute    = $this->config['fields']['attribute'];
        $fieldsHidden       = $this->config['fields']['hidden'];
        $fieldsUsingTable   = $this->config['fields']['using_table'];
        $fieldsUsingColumn  = $this->config['fields']['using_column'];
        $fieldsUsingWhere   = $this->config['fields']['using_where'];

        $headers = array();

        $sql = 'SELECT * FROM '. $fieldsTable .' f,'. $processTable .' p WHERE f.'. $id .' = p.'. $id .' AND '. $processCode .' = "'. $process_code .'"';

        $db->query($sql);
        while($db->next_record()) {

            $headers[] =  array(
                'name' => $db->f($fieldsName),
                'code' => $db->f($fieldsCode),
                'type' => $db->f($fieldsType),
                'attr' => $db->f($fieldsAttribute),
                'hidden' => $db->f($fieldsHidden),
                'using_table' => $db->f($fieldsUsingTable),
                'column' => $db->f($fieldsUsingColumn),
                'where' => $db->f($fieldsUsingWhere),
            );
        }

        if(!$process_code) {
            $this->headers = $headers;
        }
        return $headers;
    }

    public function  getProcessLinesHeaders(){
        global $db;
        $lines_tables = array();
        $sql = 'SELECT uc_minor FROM uto_connections WHERE uc_major = "'. $this->doc_code .'";';
        $db->query($sql);
        while($db->next_record()) {
            $lines_tables[] = $db->f('uc_minor');
        }

        foreach ($lines_tables as $lines_table){
            $lines_headers[$lines_table] = $this->getProcessHeaders($lines_table);
        }
        return $lines_headers;
    }

    public function getDocuments($headers, $search_string=0, $filters=0, $order=0)
    {
        global $db;

        $process_code       = $this->doc_code;

        $prefix             = $this->config['prefixes']['table'];

//        $index_code         = $this->config['indexes']['code'];
//        $index_id           = $this->config['indexes']['id'];
//        $index_line_number  = $this->config['indexes']['line_number'];

        $condition = $this->prepareConditionSql($search_string,$filters);

        $sql = 'SELECT * FROM '. $prefix . $process_code;
        if($condition)
            $sql .= ' WHERE '. $condition;
        if($order)
            $sql .= ' ORDER BY ' . $order;

        $db->query($sql);

        $rows=array();
        $i=1;
        while($db->next_record()) {
//            $rows[$i]['code'] = $db->f($index_code);
//            $rows[$i]['id'] = $db->f($index_id);
//            $rows[$i]['line_number'] = $db->f($index_line_number);
            foreach($headers as $header){
                $rows[$i][$header['code']]['value'] = $db->f($header['code']);
                $rows[$i][$header['code']]['attr'] = $header['attr'];
            }
            $i++;
        }
        return $rows;

    }

    private function prepareSearchConditionSql($search_string)
    {
        $headers = $this->headers;

        $conditions = array();
        foreach ($headers as $header){
            if(!contains($header['attr'],'S'))
                continue;

            $conditions[] = $header['code'] .' LIKE "%'. $search_string .'%"';
        }
        $sql = '(1=1)';
        if(count($conditions))
            $sql = '('. implode(' OR ',$conditions) .')';

        return $sql;
    }

    private function prepareFilterConditionSql($filters)
    {
        $conditions = array();
        foreach($filters as $key => $filter){
            if(empty($filter))
                continue;
            if(is_array($filter)){
                $multi_conditions = array();
                foreach ($filter as $key2 => $multi_filter){
                    $value = $multi_filter;
                    if (!is_numeric($multi_filter)) {
                        $value = '"' . $value . '"';
                    }
                    $multi_conditions[] = 'CONCAT(", ",' . $key . ',",") LIKE "%, ' . $multi_filter . ',%"';
                }
                $conditions[] = '('. implode(' OR ',$multi_conditions) .')';
            }
            else {
                $value = $filter;
                if (!is_numeric($filter)) {
                    $value = '"' . $value . '"';
                }
                $conditions[] = $key . ' = ' . $value;
            }
        }

        $sql = '(1=1)';
        if(count($conditions))
            $sql = implode(' AND ',$conditions);

        return $sql;
    }

    private function prepareConditionSql($search_string,$filters){


        $conditions = array();
        if($search_string)
            $conditions[] = $this->prepareSearchConditionSql($search_string);
        if($filters)
            $conditions[] = $this->prepareFilterConditionSql($filters);

        $sql = implode(' AND ',$conditions);

        return $sql;
    }

    public function prepareDistinctFieldNames($field_name,$search_string,$filters)
    {
        global $db;

        $process_code       = $this->doc_code;

        $prefix             = $this->config['prefixes']['table'];

        $condition = $this->prepareConditionSql($search_string,$filters);


        $sql = 'SELECT distinct '.$field_name.' FROM '. $prefix . $process_code;
        if($condition)
            $sql .= ' WHERE '. $condition;

        $db->query($sql);
        $distincts = array();
        while($db->next_record()){
            $distincts[] = $db->f($field_name);
        }
        return $distincts;
    }

    public function checkConnecton()
    {
        global $db;
        $sql = 'SELECT * FROM uto_headers';
        $db->query($sql);
        while($db->next_record()){
            $processes[] = array(
                'name' => $db->f(uh_name)
            );
        }
        return $processes;

    }

}