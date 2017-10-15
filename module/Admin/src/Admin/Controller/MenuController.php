<?php

class Admin_MenuController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
    }

    public function indexAction() 
    {
        $mapper = new \Admin\Model\MenuMapper();
        if ($this->_request->isPost()) {
            $id_array = $this->_getParam("id");
            $text_array = $this->_getParam("text");
            for ($i = 0, $n = count($id_array); $i < $n; $i++) {
                $data = array(
                    'text' => $text_array[$i]
                );
                $mapper->save($data, "id=" . $id_array[$i]);
            }
            Core::message()->addSuccess('Lưu thành công');
            $this->_helper->redirector('index', 'menu', 'admin');
        }
        
        $this->view->data = $mapper->getData();
    }

}
