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
        $model = new \Application\Model\Table();
        $model->setTableName('home_content');
        $rows = $model->getAll();
        $row = $rows[0];
        return new ViewModel(array('content' => $row['content']));
    }

    public function guideAction() {
        \Zend\Common\Download::download("guide/");
    }

    public function loginAction() {
        $username = $this->getRequest()->getPost('username', null);
        $password = $this->getRequest()->getPost('password', null);
        $index = new Admin_Model_IndexMapper();
        if ($index->login($username, $password)) {
            echo '';
        } else {
            echo 'error';
        }
        return;
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
