<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class HomecontentController extends AbstractActionController 
{



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
            $session = new \Zend\Session\Container('base');$session->offsetSet('message', 'Lưu thành công');
        } else {
            Core::message()->addSuccess('Bị lỗi. Gọi điện cho Tuệ');
        }
        return $this->redirect()->toUrl('/admin/homecontent'); 
    }

}
