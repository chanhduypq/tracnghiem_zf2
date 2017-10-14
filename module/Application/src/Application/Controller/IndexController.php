<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController {

    public function indexAction() {
        $model = new \Application\Model\Table('home_content');
        $rows = $model->getAll();
        $row = $rows[0];
        
//        $model = new \Application\Model\Table('user');
//        $rows = $model->select("email = 'cuongchp@gmail.com' and password='". sha1('123456')."'")->current();//->toArray();
//        var_dump((array)$rows);
        return new ViewModel(array('content' => $row['content']));
    }

    public function guideAction() {
        \Zend\Common\Download::download("guide/");
    }

    public function loginAction() {
        $username = $this->getRequest()->getPost('username', null);
        $password = $this->getRequest()->getPost('password', null);
        $model = new \Application\Model\Table('user');
        $rows = $model->select("email = '$username' and password='". sha1($password)."'")->toArray();
        if (is_array($rows)&&count($rows)>0) {
            echo '';
        } else {
            echo 'error';
        }
        return $this->getResponse();
    }

    public function logoutAction() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_helper->redirector('index', 'index', 'default');
    }

    public function logoutajaxAction() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        exit;
    }

}
