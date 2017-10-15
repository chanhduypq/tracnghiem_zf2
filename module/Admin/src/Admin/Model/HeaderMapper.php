<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class HeaderMapper extends AbstractTableGateway 
{

    public function save($data) 
    {
        
        try {
            $this->table = 'header_text';
            $this->update($data);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function getData() 
    {
        try {
            $this->table = 'header_text';
            $ret = $this->select()->toArray();
        } catch (Exception $e) {
            return array();
        }
        return $ret[0];
    }

}
