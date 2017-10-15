<?php
namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class NganhngheQuestion extends AbstractTableGateway 
{

    public $table="nganhnghe_question";    
    public function __construct($tableName=null) 
    {
        if ($this->table == NULL) {
            $this->table = $tableName;
        }
    }
    
    

}

?>