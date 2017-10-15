<?php

class Admin_IndexController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
    }

    public function indexAction() 
    {
        $username = $password = '';
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            if (isset($identity['user']) && $identity['user'] == 'admin') {
                $this->_helper->redirector('index', 'nganhnghe', 'admin');
            }
        }

        $loginResult = $this->_request->getParam('loginResult');
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
        $username = $this->_request->getParam('username', null);
        $password = $this->_request->getParam('password', null);
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
                $session->username=$this->_getParam('username');
                $session->password=$this->_getParam('password');
                $this->_helper->redirector('index', 'index', 'admin', array('loginResult' => '0'));
            }
        }
    }

    public function logoutAction() 
    {
        $auth = Zend_Auth::getInstance();
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
        $oldPassword = $this->_request->getParam('oldPassword');
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();

        if ($identity['password'] != sha1($oldPassword)) {
            echo 'error';
            return;
        }
        $newPassword = $this->_request->getParam('newPassword');
        $index = new \Admin\Model\IndexMapper();
        $index->changePassword($identity['email'], $newPassword);
        echo "";
    }

}
