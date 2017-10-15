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
                $this->_helper->redirector('index', 'nganhnghe', 'admin');
            }
        }

        $loginResult = $this->getRequest()->getPost('loginResult');
        if ($loginResult == '0') {
            $this->view->loginResult = "Thông tin bạn vừa nhập không đúng.";
            $session=new Zend_Session_Namespace('login');
            $username=$session->username;
            $password=$session->password;
            $session->unsetAll();
        }
        $this->view->username = $username;
        $this->view->password = $password;
    }

    public function loginAction() 
    {
        $username = $this->getRequest()->getPost('username', null);
        $password = $this->getRequest()->getPost('password', null);
        if ($username == null || $password == NULL) {
            $this->_helper->redirector('index', 'index', 'admin');
        } else {
            $index = new \Admin\Model\IndexMapper();
            if ($index->loginAdmin($username, $password)) {
                $session = new Zend_Session_Namespace('url');
                $controller = $session->controller;
                $session->unsetAll();
                $this->_helper->redirector('index', $controller, 'admin');
            } else {
                $session=new Zend_Session_Namespace('login');
                $session->username=$this->getRequest()->getPost('username');
                $session->password=$this->getRequest()->getPost('password');
                $this->_helper->redirector('index', 'index', 'admin', array('loginResult' => '0'));
            }
        }
    }

    public function logoutAction() 
    {
        $session = new \Zend\Session\Container('base');
        $auth->clearIdentity();
        $this->_helper->redirector('index', 'index', 'admin');
    }

    public function changepasswordAction() 
    {
        $this->disableLayout();
    }

    public function ajaxchangepasswordAction() 
    {
        $this->disableLayout();
        $oldPassword = $this->getRequest()->getPost('oldPassword');
        $session = new \Zend\Session\Container('base');
        $identity = $session->offsetGet('user');

        if ($identity['password'] != sha1($oldPassword)) {
            echo 'error';
            return;
        }
        $newPassword = $this->getRequest()->getPost('newPassword');
        $index = new \Admin\Model\IndexMapper();
        $index->changePassword($identity['email'], $newPassword);
        echo "";
    }

}
