<?php

namespace Wj\DocBot;

class SpreadSheetStack
{
    private $id;
    private $_cache;
    private $lastUpdateTime;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getData()
    {
        if (null === $this->_cache || $lastUpdateTime - time() >= 60) {
            $this->loadData();
        }

        return $this->_cache;
    }

    protected function loadData()
    {
        $url = "https://docs.google.com/spreadsheet/pub?key=".$this->id."&single=true&gid=0&output=csv";

        if(!ini_set('default_socket_timeout', 15)) {
            throw new \RuntimeException("unable to change socket timeout");
        }

        $sp_data = array();

        if (($handle = fopen($url, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $sp_data[] = $data;
            }
            fclose($handle);
        } else {
            throw new \RuntimeException("Problem reading csv");
        }

        $this->_cache = $sp_data;
        $this->lastUpdateTime = time();
    }
}
