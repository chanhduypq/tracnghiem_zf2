<?php
namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class Userreviewdetail extends AbstractTableGateway 
{

    public $table = "user_review_detail";

    public function __construct($tableName=null) 
    {
        if ($this->table == NULL) {
            $this->table = $tableName;
        }
    }

}

?>