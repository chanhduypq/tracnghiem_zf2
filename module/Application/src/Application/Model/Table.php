<?php

namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

/**
 * Album model
 *
 * @author suleymanmelikoglu
 */
class Table extends AbstractTableGateway {

    public function __construct($tableName=null) {
        $this->table = $tableName;
    }
    
    public function setTableName($tableName){
        $this->table=$tableName;
    }

    public function get($id) {
        $id = (int) $id;
        return $this->select("id = " . $id)->toArray();
    }

    public function getAll() {
        return $this->select()->toArray();
    }

//    public function addAlbum($artist, $title) {
//        $data = array(
//            'artist' => $artist,
//            'title' => $title
//        );
//        $this->insert($data);
//    }
//
//    public function updateAlbum($id, $artist, $title) {
//        $data = array(
//            'artist' => $artist,
//            'title' => $title
//        );
//        $this->update($data, "id = " . (int) $id);
//    }
//
//    public function deleteAlbum($id) {
//        $this->delete("id = " . $id);
//    }

}
