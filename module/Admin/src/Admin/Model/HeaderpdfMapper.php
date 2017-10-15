<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class HeaderpdfMapper extends AbstractTableGateway 
{

    public function save($data) 
    {
        try {
            $this->table='header_pdf';
            $data['json']=json_encode($data['text']);
            unset($data['text']);
            unset($data['content']);
            $this->update($data);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function getContent() 
    {

        try {
            $this->table = 'header_pdf';
            $ret = $this->select()->toArray();
        } catch (Exception $e) {
            return array();
        }
        return $ret[0];
    }
    
    public static function getHeader(){
        try {
            $this->table = 'header_pdf';
            $ret = $this->select()->toArray();
        } catch (Exception $e) {
            return '';
        }
        return $ret[0]['json'];
    }

}
