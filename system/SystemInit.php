<?php


class SystemInit
{
    const aaaa = 1;
    private $db;
    private $head_table;
    private $code_column;
    private $name_column;

    public function __construct($db,$head_table,$code_column,$name_column)
    {
        $this->db = $db;
        $this->head_table = $head_table;
        $this->code_column = $code_column;
        $this->name_column = $name_column;
    }

    public function get_processes()
    {
        $db = $this->db;
        $head_table = $this->head_table;
        $name_column = $this->name_column;
        $code_column = $this->code_column;

        $sql = 'SELECT '. $code_column .', '. $name_column .' FROM '. $head_table .';';
        $db->query($sql);
        while($db->next_record()){
            $processes[] = array(
                'code' => $db->f($code_column),
                'name' => $db->f($name_column)
            );
        }
        return $processes;
    }
}