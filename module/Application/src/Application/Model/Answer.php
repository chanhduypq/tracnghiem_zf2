<?php
namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class Answer extends AbstractTableGateway 
{

    public $table="answer";    
    public function __construct($tableName=null) 
    {
        if ($this->table == NULL) {
            $this->table = $tableName;
        }
    }
    public function getAnswers($question_id) 
    {          
        $select = new \Zend\Db\Sql\Select();
        $select->from('answer')->order('sign ASC')->where('question_id='.$question_id);
        $items=  $this->selectWith($select)->toArray();
        return $items;
    }   
    
    public function getAnswer($id) 
    {                 
        $item=  $this->select("id=$id")->toArray();
        return $item[0];       
    }   
      
    

}

?>