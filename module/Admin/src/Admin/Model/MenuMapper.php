<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class MenuMapper extends AbstractTableGateway 
{

    public function save($data, $where) 
    {
        try {
            $this->table='menu';
            $this->update($data,$where);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function getData() 
    {
        
        try {
            $this->table = 'menu';
            $ret = $this->select()->toArray();
        } catch (Exception $e) {
            return array();
        }
        return $ret;
    }

}
