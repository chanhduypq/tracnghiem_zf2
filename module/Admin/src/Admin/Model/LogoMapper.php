<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class LogoMapper extends AbstractTableGateway 
{

    public $table = 'logo';
    
    public function save($item_image, $dynamic) 
    {
        $data = array();
        $data['file_name'] = $item_image;
        $data['dynamic'] = $dynamic;

        try {
            $ret= $this->select()->toArray();
            $file_name = $ret[0]['file_name'];
            if ($file_name == '') {
                $this->insert($data);
            } else {
                if ($item_image == null || trim($item_image == "")) {
                    unset($data['file_name']);
                }
                $this->update($data);
            }
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
