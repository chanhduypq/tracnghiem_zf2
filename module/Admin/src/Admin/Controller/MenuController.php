<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class MenuController extends AbstractActionController 
{



    public function indexAction() 
    {
        $mapper = new \Admin\Model\MenuMapper();
        if ($this->_request->isPost()) {
            $id_array = $this->getRequest()->getPost("id");
            $text_array = $this->getRequest()->getPost("text");
            for ($i = 0, $n = count($id_array); $i < $n; $i++) {
                $data = array(
                    'text' => $text_array[$i]
                );
                $mapper->save($data, "id=" . $id_array[$i]);
            }
            $session = new \Zend\Session\Container('base');$session->offsetSet('message', 'Lưu thành công');
            
            return $this->redirect()->toUrl('/admin/menu'); 
        }
        
        $this->view->data = $mapper->getData();
    }

}
