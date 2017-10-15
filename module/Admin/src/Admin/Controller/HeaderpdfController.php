<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class HeaderpdfController extends AbstractActionController 
{



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
