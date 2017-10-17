<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class IndexController extends AbstractActionController 
{



    public function indexAction() 
    {        
        $username = $password = '';
        $session = new \Zend\Session\Container('base');
        if ($session->offsetExists('user')) {
            $identity = $session->offsetGet('user');
            if (isset($identity['user']) && $identity['user'] == 'admin') {
                return $this->redirect()->toUrl('/admin/nganhnghe'); 
            }
        }

        $loginResult = $this->params()->fromQuery('loginResult');
        if ($loginResult === '0') {
            $loginResult = "Thông tin bạn vừa nhập không đúng.";
            $session = new \Zend\Session\Container('base');
            $username=$session->offsetGet('username');
            $password=$session->offsetGet('password');
            $session->unsetAll();
        }
        else{
            $loginResult='';
        }
        
        return new ViewModel(array('username' => $username,'password' => $password,'loginResult' => $loginResult));
    }

    public function loginAction() 
    {
        $username = $this->getRequest()->getPost('username', null);
        $password = $this->getRequest()->getPost('password', null);
        if ($username == null || $password == NULL) {
            
            return $this->redirect()->toUrl('/admin/index'); 
        } else {
            $index = new \Admin\Model\IndexMapper();
            if ($index->loginAdmin($username, $password)) {
                $session = new \Zend\Session\Container('base');
                $controller = $session->offsetGet('controller');
                $session->offsetUnset('controller');
                if ($controller == NULL) {
                    $controller = 'nganhnghe';
                }
                return $this->redirect()->toUrl('/admin/'.$controller); 
            } else {
                
                $session = new \Zend\Session\Container('base');
                $session->offsetSet('username', $this->getRequest()->getPost('username'));
                $session->offsetSet('password', $this->getRequest()->getPost('password'));
                
                return $this->redirect()->toUrl('/admin/index?loginResult=0'); 
            }
        }
    }

    public function logoutAction() 
    {
        $session = new \Zend\Session\Container('base');
        $session->offsetUnset('user');
        return $this->redirect()->toUrl('/admin/index'); 
        
    }

    public function changepasswordAction() 
    {
        
    }

    public function ajaxchangepasswordAction() 
    {
        
        $oldPassword = $this->getRequest()->getPost('oldPassword');
        $session = new \Zend\Session\Container('base');
        $identity = $session->offsetGet('user');

        if ($identity['password'] != sha1($oldPassword)) {
            echo 'error';
            return $this->getResponse();
        }
        $newPassword = $this->getRequest()->getPost('newPassword');
        $index = new \Admin\Model\IndexMapper();
        $index->changePassword($identity['email'], $newPassword);
        echo "";
        return $this->getResponse();
    }

}
