<?php
namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class User extends AbstractTableGateway 
{

    public $table = "user";

    public function __construct($tableName=null) 
    {
        if ($this->table == NULL) {
            $this->table = $tableName;
        }
    }

    public function getUsers(&$total, $limit = null, $start = null) 
    {
        
        $select = new \Zend\Db\Sql\Select();


        if (\Zend\Common\Numeric::isInteger($limit) && \Zend\Common\Numeric::isInteger($start)) {
            $select->from('user')->where('is_admin is null OR is_admin=0')->order('id ASC')->offset($start)->limit($limit);
        } else {
            $select->from('user')->where('is_admin is null OR is_admin=0')->order('id ASC');
        }

        $items = $this->selectWith($select)->toArray();

        $select = new \Zend\Db\Sql\Select();
        $select->columns(array('num' => new \Zend\Db\Sql\Expression('COUNT(*)')))->from('user')->where('is_admin is null OR is_admin=0');
        $total = $this->selectWith($select)->toArray();
        $total = $total[0]['num'];

        return $items;
    }

    public function getUser($id) 
    {
        $item = $this->select("id=$id")->toArray();
        return $item[0];
    }

}

?>