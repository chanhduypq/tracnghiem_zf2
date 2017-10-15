<?php
namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class Userexamdetail extends AbstractTableGateway 
{

    public $table = "user_exam_detail";

    public function __construct($tableName=null) 
    {
        if ($this->table == NULL) {
            $this->table = $tableName;
        }
    }

}

?>