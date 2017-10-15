<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class HinhnentrangchuMapper extends AbstractTableGateway 
{
    
    public $table = 'home_content';

    public function save($item_image) 
    {
        $data = array();
        $data['bg'] = $item_image;


        try {
            $ret= $this->select()->toArray();
            $file_name = $ret[0]['bg'];
            $this->update($data);
        } catch (Exception $e) {
            return array('success' => false, 'file_name' => $file_name);
        }
        return array('success' => TRUE, 'file_name' => $file_name);
    }

    public function getInfo() 
    {
        try {
            $ret = $this->select()->toArray();
        } catch (Exception $e) {
            return array();
        }
        return $ret[0];
    }

}
