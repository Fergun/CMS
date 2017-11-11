<?php

class Process
{
    private $doc_code;
    private $config;


    public function __construct($config,$doc_code)
    {
        $this->doc_code = $doc_code;
        $this->config = $config;
    }

    public function getProcessStatus()
    {
        $table              = $this->doc_code;
        $processTable       = $this->config['headers']['table'];
        $processCode        = $this->config['headers']['code'];

        $statusTable        = $this->config['statuses']['table'];
        $statusValue        = $this->config['statuses']['value'];
        $statusName         = $this->config['statuses']['name'];
        $statusNextValue    = $this->config['statuses']['next_value'];
        $statusAction       = $this->config['statuses']['action'];

        global $db;
        $sql = 'SELECT u_status FROM '. $processTable . ' WHERE '. $processCode .' = "'. $table .'"' ;
        $db->query($sql);
        if($db->next_record()){
            $status = $db->f('u_status');
            $sql = 'SELECT '.$statusValue .', '. $statusName .' FROM '. $statusTable .' WHERE u_code = "headers" AND '. $statusValue .' = '. $status;
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
        $table              = $this->doc_code;
        $processTable       = $this->config['headers']['table'];
        $statusTable        = $this->config['statuses']['table'];
        $statusValue        = $this->config['statuses']['value'];
        $statusNextValue    = $this->config['statuses']['next_value'];
        $statusAction       = $this->config['statuses']['action'];

        global $db;
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

    public function get_status($head_table, $status_table, $code_column, $name_column)
    {
        global $db;


        $sql = 'SELECT '. $name_column .' FROM '. $status_table .' WHERE u_code = '. $head_table .' AND us_value = '. $code_column .';';
        $db->query($sql);
        while($db->next_record()){
            $processes[] = array(
                'name' => $db->f($name_column)
            );
        }
        return $processes;
    }

//    public function get_action($head_table, $status_table, $action_name_column, $status_id)
//    {
//        $db = $this->db;
//
//
//        $sql = 'SELECT '. $action_name_column .' FROM '. $status_table .' WHERE u_code = "'. $head_table .'" AND us_value = '. $status_id .';';
//        echo '<h1><pre>';
//        var_dump($sql);
//        echo '</pre></h1>';
//        $db->query($sql);
//        while($db->next_record()){
//            $actions[] = array(
//                'action' => $db->f($action_name_column)
//            );
//        }
//        return $actions;
//    }
}