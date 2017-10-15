<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class HeaderfooterController extends AbstractActionController 
{



    public function indexAction() 
    {
        $mapper_header = new \Admin\Model\HeaderMapper(); 
        $mapper_footer = new \Admin\Model\FooterMapper();
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            
                       
            $mapper_header->save(array('dynamic'=>$data['dynamic'],'text'=>$data['header_text']));

            
            $mapper_footer->save(array('text'=>$data['footer_text']));
            Core::message()->addSuccess('Lưu thành công');
            $this->_helper->redirector('index', 'headerfooter', 'admin');
        }
        
        $row= $mapper_header->getData();
        $this->view->header_text = $row['text'];
        $this->view->dynamic = $row['dynamic'];
        
        $item = $mapper_footer->getNoiDung();
        
        $noi_dung = '';
        if (is_array($item) && count($item) > 0) {
            $noi_dung = $item['text'];
        }
        
        $this->view->footer_text = $noi_dung;
    }

}
