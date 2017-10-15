<?php
namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class Userpass extends AbstractTableGateway 
{

    public $table = "user_pass";

    public function __construct($tableName=null) 
    {
        if ($this->table == NULL) {
            $this->table = $tableName;
        }
    }

}

?>