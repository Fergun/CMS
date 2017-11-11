<?php


class System_Init
{
    const aaaa = 1; /// kiedy¶ wykorzystam narazie nie
    private $db;
    public $config;

    public function __construct($db,Config $config)
    {
        $this->db = $db;
        $this->config = $config->getData();
    }

    public function getProcessesList()
    {
        $db             = $this->db;
        $process_table  = $this->config['headers']['table'];
        $process_code   = $this->config['headers']['code'];
        $process_name   = $this->config['headers']['name'];

        $sql = 'SELECT '. $process_code .', '. $process_name .' FROM '. $process_table .';';
        $db->query($sql);
        while($db->next_record()){
            $processes[] = array(
                'code' => $db->f($process_code),
                'name' => $db->f($process_name)
            );
        }
        return $processes;
    }

    public function getProcess($doc_code)
    {
        $process = new Process($this->config,$doc_code);
        $status = $process->getProcessStatus();
        $actions = $process->getProcessActions($status['value']);
        $process = array(
            'status' => $status,
            'actions' => ($actions ? $actions : 0)
        );
        return $process;
    }
}