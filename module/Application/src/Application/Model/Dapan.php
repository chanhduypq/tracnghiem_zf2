<?php
namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class Dapan extends AbstractTableGateway 
{

    public $table="dap_an";    
    public function __construct($tableName=null) 
    {
        if ($this->table == NULL) {
            $this->table = $tableName;
        }
    }
    
    

}

?>