<?php

class Document
{
    private $headers;
    private $doc_param;
    private $lines_headers;


    public function __construct($doc_param,$headers,$lines_headers)
    {
        $this->doc_param = $doc_param;
        $this->headers = $headers;
        $this->lines_headers = $lines_headers;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeadersData()
    {
        global $header_code;

        $headers = $this->headers;
        $doc_param = $this->doc_param;
        global $db;
        if(!$doc_param['code']){
            $doc_param['line_number'] = 1;
        }
        $sql = 'SELECT * FROM uto_'. $header_code .' WHERE u_code = "'. $doc_param['code'] .'" AND u_id = '. $doc_param['id'] .' AND u_line_number = '. $doc_param['line_number'] .';';
        $db->query($sql);
        if($db->next_record()) {
            foreach($headers as $header){
                $document[$header['code']] = $db->f($header['code']);
            }

        }
        return $document;
    }

    public function getLines()
    {
        return $this->lines_headers;
    }

    public function getLinesData()
    {
        global $db, $header_code;

        $lines_headers = $this->lines_headers;
        $doc_param = $this->doc_param;

        $lines = array();

        if(!$doc_param['code']){
            $doc_param['line_number'] = 1;
        }

        foreach($lines_headers as $lines_code => $line_headers) {
            $i = 0;

            $sql = 'SELECT * FROM uto_' . $lines_code . ' WHERE u_code = "'. $header_code .'" AND u_id=' . $doc_param['id'];
            $db->query($sql);
            while ($db->next_record()) {
                foreach ($line_headers as $line_header) {
                    $lines[$lines_code][$i][$line_header['code']] = $db->f($line_header['code']);
                }
                $i++;
            }

        }
        return $lines;
    }



}