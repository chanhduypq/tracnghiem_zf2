<?php
namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class Nganhnghe extends AbstractTableGateway 
{

    public $table = "nganh_nghe";

    public function __construct($tableName=null) 
    {
        if ($this->table == NULL) {
            $this->table = $tableName;
        }
    }

    public function getNganhNghes() 
    {
        $items = $this->select()->toArray();
        return $items;
    }

    

}

?>