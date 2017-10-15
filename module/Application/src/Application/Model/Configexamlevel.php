<?php
namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class Configexamlevel extends AbstractTableGateway 
{

    public $table="config_exam_level";    
    public function __construct($tableName=null) 
    {
        if ($this->table == NULL) {
            $this->table = $tableName;
        }
    }
    public function getConfigExamLevels() 
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('config_exam_level')->order('level');
        $items=  $this->selectWith($select)->toArray();
        
        return $items;        
    }     
      
    

}

?>