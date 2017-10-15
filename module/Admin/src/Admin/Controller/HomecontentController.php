<?php

class Admin_HomecontentController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
    }

    public function indexAction() 
    {
        $mapper = new \Admin\Model\HomecontentMapper();
        $item = $mapper->getContent();
        $noi_dung = '';
        if (is_array($item) && count($item) > 0) {
            $noi_dung = $item['content'];
        }
        $this->view->content = $noi_dung;
        
    }

    public function saveAction() 
    {
        $data = $this->_request->getPost();
        $item = new \Admin\Model\HomecontentMapper();
        $result = $item->save($data);
        if ($result == true) {
            Core::message()->addSuccess('Lưu thành công');
        } else {
            Core::message()->addSuccess('Bị lỗi. Gọi điện cho Tuệ');
        }
        $this->_helper->redirector('index', 'homecontent', 'admin');
    }

}
