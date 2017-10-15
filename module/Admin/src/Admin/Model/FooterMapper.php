<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class FooterMapper extends AbstractTableGateway 
{

    public function save($data) 
    {
        try {
            $this->table='footer';
            $this->update($data);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function getNoiDung() 
    {
        try {
            $this->table = 'footer';
            $ret = $this->select()->toArray();
        } catch (Exception $e) {
            return array();
        }
        return $ret[0];
    }

    

}
