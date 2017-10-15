<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class HomecontentMapper extends AbstractTableGateway 
{

    public function save($data) 
    {
        try {
            $this->table='home_content';
            $this->update($data);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function getContent() 
    {

        try {
            $this->table = 'home_content';
            $ret = $this->select()->toArray();
        } catch (Exception $e) {
            return array();
        }
        return $ret[0];
    }

}
