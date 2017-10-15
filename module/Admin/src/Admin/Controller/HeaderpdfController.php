<?php

class Admin_HeaderpdfController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
    }

    public function indexAction() 
    {
        $mapper = new \Admin\Model\HeaderpdfMapper();
        $item = $mapper->getContent();
        $noi_dung = $json = '';
        if (is_array($item) && count($item) > 0) {
            $noi_dung = $item['content'];
            $json = $item['json'];
        }
        $this->view->content = $noi_dung;
        $this->view->json = $json;
        $this->view->message = $this->getMessage();
    }

    public function saveAction() 
    {
        $data = $this->_request->getPost();
        $item = new \Admin\Model\HeaderpdfMapper();
        $result = $item->save($data);
        if ($result == true) {
            Core::message()->addSuccess('Lưu thành công');
        } else {
            Core::message()->addSuccess('Bị lỗi. Gọi điện cho Tuệ');
        }
        $this->_helper->redirector('index', 'headerpdf', 'admin');
    }

}
